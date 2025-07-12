package main

import (
	"encoding/base64"
	"errors"
	"mime"
	"net/http"
	"path/filepath"
	"strconv"
	"strings"

	elogrus "github.com/dictor/echologrus"
	"github.com/labstack/echo/v4"
	"github.com/maragudk/gomponents"
	"github.com/samber/lo"
	"github.com/sirupsen/logrus"
	"github.com/spf13/afero"
	"github.com/spf13/viper"
)

var (
	GlobalLogger *logrus.Logger
)

func main() {
	// set logger
	e := echo.New()
	GlobalLogger = elogrus.Attach(e).Logger

	// set config
	viper.AutomaticEnv()
	viper.SetConfigName("config")
	viper.SetConfigType("yaml")
	viper.AddConfigPath(".")

	viper.SetDefault("ServeAddress", ":80")
	viper.SetDefault("ComicbookPath", "./content")
	viper.SetDefault("UrlPrefix", "")

	// read config
	err := viper.ReadInConfig()
	if err != nil {
		GlobalLogger.WithError(err).Warn("failed to read configuration")
	}

	// register provider
	pc := ProviderCollection{}
	pc.Register(RawProvider{})

	// read books
	GlobalLogger.Info("book exploring start")
	fs := afero.NewOsFs()
	books, err := ExploreBooks(fs, viper.GetString("ComicbookPath"), pc)
	if err != nil {
		GlobalLogger.WithError(err).Fatal("failed to explore books")
	}

	providerStatistic := map[string]int{}
	for _, book := range books {
		if book.HasProvider {
			providerStatistic[book.Provider.ProviderString()] += 1
		} else {
			providerStatistic["no_provider"] += 1
		}
	}
	GlobalLogger.WithField("count", len(books)).Info("book exploring complete")
	GlobalLogger.WithFields(lo.MapEntries[string, int, string, interface{}](providerStatistic, func(key string, value int) (string, interface{}) {
		return key, value
	})).Info("book provider statistic")

	// set route
	e.GET("/", func(c echo.Context) error {
		page, err := strconv.Atoi(c.QueryParam("page"))
		if err != nil {
			page = 1
		}
		limit, err := strconv.Atoi(c.QueryParam("limit"))
		if err != nil {
			limit = 10
		}

		searchQuery := c.QueryParam("q")
		var filteredBooks []Book
		if searchQuery != "" {
			filteredBooks = lo.Filter(books, func(b Book, _ int) bool {
				return strings.Contains(strings.ToLower(b.Name), strings.ToLower(searchQuery)) ||
					strings.Contains(strings.ToLower(b.Author), strings.ToLower(searchQuery)) ||
					lo.SomeBy(b.Tag, func(t string) bool {
						return strings.Contains(strings.ToLower(t), strings.ToLower(searchQuery))
					})
			})
		} else {
			filteredBooks = books
		}

		paginatedBooks, totalPage := Paginate(filteredBooks, page, limit)

		var component []gomponents.Node
		if c.Request().Header.Get("HX-request") == "true" {
			component = BookCardTemplate(paginatedBooks, page, totalPage, limit)
		} else {
			component = []gomponents.Node{BaseTemplate(BookCardTemplate(paginatedBooks, page, totalPage, limit)...)}
		}

		for _, com := range component {
			if err := com.Render(c.Response().Writer); err != nil {
				e.Logger.Error(err)
				return c.NoContent(http.StatusInternalServerError)
			}
		}
		return nil
	})

	e.GET("/viewer/:bookId/:page", func(c echo.Context) error {
		bookId := c.Param("bookId")
		page := c.Param("page")

		if len(bookId) == 0 || len(page) == 0 {
			GlobalLogger.WithFields(logrus.Fields{
				"bookId": bookId,
				"page":   page,
			}).Errorf("requested with empty parameter")
			return c.NoContent(http.StatusBadRequest)
		}

		intPage, err := strconv.ParseInt(page, 10, 0)
		if err != nil {
			GlobalLogger.WithFields(logrus.Fields{
				"bookId": bookId,
				"page":   page,
			}).WithError(err).Errorf("requested with invalid page number")
			return c.NoContent(http.StatusBadRequest)
		}

		targetBook, bookExist := lo.Find(books, func(item Book) bool {
			return item.ID == BookId(bookId)
		})

		if !bookExist {
			GlobalLogger.WithFields(logrus.Fields{
				"bookId": bookId,
				"page":   page,
			}).WithError(err).Errorf("requested book id not found")
			return c.NoContent(http.StatusNotFound)
		}

		if intPage < 1 || int(intPage) > targetBook.ImageCount {
			GlobalLogger.WithFields(logrus.Fields{
				"bookId": bookId,
				"page":   page,
			}).WithError(err).Errorf("requested book page number is invalid")
			return c.NoContent(http.StatusBadRequest)
		}

		var component []gomponents.Node
		if c.Request().Header.Get("HX-request") == "true" {
			component = ImageViewerTemplate(targetBook, int(intPage))
		} else {
			component = []gomponents.Node{BaseTemplate(ImageViewerTemplate(targetBook, int(intPage))...)}
		}

		for _, com := range component {
			if err := com.Render(c.Response().Writer); err != nil {
				e.Logger.Error(err)
				return c.NoContent(http.StatusInternalServerError)
			}
		}

		return nil
	})

	e.GET("/image/:path", func(c echo.Context) error {
		path := c.Param("path")
		decodedPath, err := base64.StdEncoding.DecodeString(path)
		if err != nil {
			GlobalLogger.WithField("path", path).Errorf("requested with invalid base64 path")
			return c.NoContent(http.StatusBadRequest)
		}

		baseFs := afero.NewBasePathFs(fs, viper.GetString("ComicbookPath"))
		stringPath := string(decodedPath)
		image, err := ImageFile(baseFs, stringPath)
		var fsErrDef *FileSystemError
		if err == nil {
			mimeType := mime.TypeByExtension(filepath.Ext(filepath.Base(stringPath)))
			GlobalLogger.WithFields(logrus.Fields{
				"path": stringPath,
				"mime": mimeType,
			}).Info("response image file")
			return c.Blob(http.StatusOK, mimeType, image)
		} else if errors.As(err, &fsErrDef) {
			GlobalLogger.WithError(err).WithField("path", stringPath).Error("failed to read requested image path, file system error")
			return c.NoContent(http.StatusInternalServerError)
		} else if errors.Is(err, ErrFileIsNotImage) {
			GlobalLogger.WithError(err).WithField("path", stringPath).Error("requested image path doesn't direct image file, ignore")
			return c.NoContent(http.StatusNotFound)
		} else if errors.Is(err, ErrFileNotFound) {
			GlobalLogger.WithError(err).WithField("path", stringPath).Error("requested image path not found")
			return c.NoContent(http.StatusNotFound)
		} else {
			GlobalLogger.WithError(err).Error("failed to read requested image path, unknown image reading error")
			return c.NoContent(http.StatusInternalServerError)
		}
	})

	e.Logger.Fatal(e.Start(viper.GetString("ServeAddress")))
}

// Paginate given slice
func Paginate[T any](slice []T, page int, limit int) ([]T, int) {
	totalPage := len(slice) / limit
	if len(slice)%limit != 0 {
		totalPage++
	}

	if page < 1 {
		page = 1
	} else if page > totalPage {
		page = totalPage
	}

	start := (page - 1) * limit
	end := start + limit
	if start > len(slice) {
		start = len(slice)
	}
	if end > len(slice) {
		end = len(slice)
	}

	return slice[start:end], totalPage
}
