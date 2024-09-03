package main

import (
	"encoding/base64"
	"errors"
	"mime"
	"net/http"
	"path/filepath"

	elogrus "github.com/dictor/echologrus"
	"github.com/labstack/echo/v4"
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
	viper.SetConfigName("config")
	viper.SetConfigType("yaml")
	viper.AddConfigPath(".")

	viper.SetDefault("ServeAddress", ":80")
	viper.SetDefault("ComicbookPath", "./content")

	// read config
	err := viper.ReadInConfig()
	if err != nil {
		GlobalLogger.WithError(err).Warn("failed to read configuration")
	}

	// register provider
	pc := ProviderCollection{}
	pc.Register(RawProvider{})

	// read books
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
		if err := BaseTemplate(BookCardTemplate(books)...).Render(c.Response().Writer); err != nil {
			e.Logger.Error(err)
			return c.NoContent(http.StatusInternalServerError)
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
			GlobalLogger.WithError(err).WithFields(logrus.Fields{
				"path": stringPath,
				"mime": mimeType,
			}).Error("response image file")
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
