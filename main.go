package main

import (
	"encoding/base64"
	"errors"
	"mime"
	"net/http"
	"path/filepath"
	"strconv"

	elogrus "github.com/dictor/echologrus"
	"github.com/labstack/echo/v4"
	"github.com/samber/lo"
	"github.com/sirupsen/logrus"
	"fmt" // Added for fmt.Sprintf

	g "github.com/maragudk/gomponents"      // Added for g.Group
	hx "github.com/maragudk/gomponents-htmx" // Added for hx attributes
	. "github.com/maragudk/gomponents/html" // Added for Div, Class etc.
	"github.com/spf13/afero"
	"github.com/spf13/viper"
	"sort"    // Added for sorting
	"strings" // Added for search filtering
	"time"    // Added for time comparison (though direct methods Before/After exist)
)

var (
	GlobalLogger *logrus.Logger
	AppFs        afero.Fs // Application-wide filesystem
)

// filterBooks performs a case-insensitive search on book name, author, and tags.
// filterBooks performs a case-insensitive search on book name, author, and tags.
func filterBooks(allBooks []Book, query string) []Book {
	if query == "" {
		return allBooks
	}
	lowerQuery := strings.ToLower(query)
	var filtered []Book

	for _, book := range allBooks {
		if strings.Contains(strings.ToLower(book.Name), lowerQuery) {
			filtered = append(filtered, book)
			continue
		}
		if strings.Contains(strings.ToLower(book.Author), lowerQuery) {
			filtered = append(filtered, book)
			continue
		}
		for _, tag := range book.Tag { // Corrected field name from Tags to Tag
			if strings.Contains(strings.ToLower(tag), lowerQuery) {
				filtered = append(filtered, book)
				break // Found in tags, move to next book
			}
		}
	}
	return filtered
)

func main() {
	// set logger
	e := echo.New()
	GlobalLogger = elogrus.Attach(e).Logger
	AppFs = afero.NewOsFs() // Initialize AppFs

	// set config
	viper.SetConfigName("config")
	viper.SetConfigType("yaml")
	viper.AddConfigPath(".")

	viper.SetDefault("ServeAddress", ":80")
	viper.SetDefault("ComicbookPath", "./content")
	viper.SetDefault("BooksPerPage", 20)

	// read config
	err := viper.ReadInConfig()
	if err != nil {
		GlobalLogger.WithError(err).Warn("failed to read configuration")
	}

	// register provider
	pc := ProviderCollection{}
	pc.Register(RawProvider{})

	// read books
	// fs := afero.NewOsFs() // AppFs is used now
	books, err := ExploreBooks(AppFs, viper.GetString("ComicbookPath"), pc)
	if err != nil {
		GlobalLogger.WithError(err).Fatal("failed to explore books") // Use GlobalLogger for fatal startup errors
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

	// Setup handlers
	h := &handlerEnv{bookList: books} // Assuming 'books' is the global var

	// set route
	e.GET("/", h.handleRoot)
	e.GET("/books", h.handleBooks)
	e.GET("/viewer/:bookId/:page", h.handleViewer)
	e.GET("/image/:path", h.handleImage)

	e.Logger.Fatal(e.Start(viper.GetString("ServeAddress")))
}

// handlerEnv can hold dependencies like the book list, logger, etc.
// For now, it just holds the book list and filesystem access.
type handlerEnv struct {
	bookList []Book
	fs       afero.Fs // For image handling and potentially other fs access
}

func (h *handlerEnv) handleRoot(c echo.Context) error {
	searchQuery := c.QueryParam("search")
	sortBy := c.QueryParam("sort_by")
		sortOrder := c.QueryParam("sort_order")
		pageStr := c.QueryParam("page")
		page, err := strconv.Atoi(pageStr)
		if err != nil || page < 1 {
			page = 1
		}

		booksPerPage := viper.GetInt("BooksPerPage")

	// Filter books based on search query
	currentBooks := filterBooks(h.bookList, searchQuery) // Use h.bookList (Corrected: removed duplicate global 'books' usage)

	// Apply sorting
	currentBooks = sortBooks(currentBooks, sortBy, sortOrder)

	// Pagination
	pagedBooks, totalPages, err := paginateBooks(currentBooks, page, booksPerPage)
	if err != nil { // Handles page out of bounds or no books after filter/sort
		pagedBooks = []Book{}
		hasPrevPage := page > 1 && totalPages > 0
		if page > totalPages && totalPages == 0 && page == 1 {
			hasPrevPage = false
		}
		if renderErr := BaseTemplate(searchQuery, BookCardTemplate(pagedBooks, page, totalPages, 1, 1, hasPrevPage, false, searchQuery, sortBy, sortOrder)).Render(c.Response().Writer); renderErr != nil {
			c.Logger().Error(renderErr)
			return c.NoContent(http.StatusInternalServerError)
		}
		return nil
	}


		hasPrevPage := page > 1
		hasNextPage := page < totalPages

		prevPage := page - 1
		if !hasPrevPage {
			prevPage = 1 // or some other sensible default if needed by template logic
		}

		nextPage := page + 1
		if !hasNextPage {
			nextPage = totalPages // or some other sensible default
		}

	// Pass searchQuery, sortBy, sortOrder to BaseTemplate and BookCardTemplate
	if err := BaseTemplate(searchQuery, BookCardTemplate(pagedBooks, page, totalPages, prevPage, nextPage, hasPrevPage, hasNextPage, searchQuery, sortBy, sortOrder)).Render(c.Response().Writer); err != nil {
		c.Logger().Error(err)
		return c.NoContent(http.StatusInternalServerError)
	}
	return nil
}

func (h *handlerEnv) handleBooks(c echo.Context) error {
	searchQuery := c.QueryParam("search")
	sortBy := c.QueryParam("sort_by")
		sortOrder := c.QueryParam("sort_order")
		pageStr := c.QueryParam("page")
		page, err := strconv.Atoi(pageStr)
		if err != nil || page < 1 {
			page = 1 // Default to page 1 if invalid or not provided
		}

		booksPerPage := viper.GetInt("BooksPerPage")

		currentBooks := filterBooks(h.bookList, searchQuery) // Use h.bookList

		// Apply sorting
		currentBooks = sortBooks(currentBooks, sortBy, sortOrder)

		// Pagination for /books
		pagedBooksForInfiniteScroll, _, err := paginateBooks(currentBooks, page, booksPerPage)
		if err != nil {
			return c.HTML(http.StatusOK, "")
		}

		hasNextPageForTrigger := (page * booksPerPage) < len(currentBooks)
		nextPageForTrigger := page + 1

		nodes := BookCardNodes(pagedBooks)

		if hasNextPageForTrigger {
			loadMoreURL := fmt.Sprintf("/books?page=%d", nextPageForTrigger)
			if searchQuery != "" {
				loadMoreURL += "&search=" + searchQuery
			}
			// Add sort parameters to the infinite scroll's next load URL
			if sortBy != "" && sortOrder != "" {
				loadMoreURL += "&sort_by=" + sortBy + "&sort_order=" + sortOrder
			}
			triggerDiv := Div(
				hx.Get(loadMoreURL),
				hx.Trigger("revealed"),
				hx.Swap("outerHTML"),
				Class("w-full h-10"), // Placeholder for the next trigger
			)
			nodes = g.Group([]g.Node{nodes, triggerDiv})
		}

		// Render the nodes directly to HTML string
		// Need to import "github.com/maragudk/gomponents/html" with . "dot" alias for Div, etc.
		// Also need a way to render g.Node to string. Echo context's Render might not be suitable for fragments.
		// A temporary solution for rendering might be needed if not available directly.
		// For now, assuming c.Render can handle g.Node directly or we adapt.
		// Let's use a helper or direct rendering if available, otherwise this part needs adjustment.
		// For the purpose of this step, let's assume a RenderNode function exists or adapt.
		// This will likely require `import "bytes"` and `node.Render(writer)`
		// For now, let's use a placeholder for rendering to proceed with logic structure.
		// This part will be tricky with current tools. Let's try c.Render and see.
		// The gomponents library usually renders to an io.Writer.
		// We might need to render to a buffer and then send as HTML.
		// This is a known pattern:
		// var buf bytes.Buffer
		// nodes.Render(&buf)
		// return c.HTML(http.StatusOK, buf.String())

		return nodes.Render(c.Response().Writer)
}

func (h *handlerEnv) handleViewer(c echo.Context) error {
	bookId := c.Param("bookId")
	pageParam := c.Param("page") // Renamed from page to avoid conflict if any

	if len(bookId) == 0 || len(pageParam) == 0 {
		c.Logger().WithFields(logrus.Fields{
			"bookId": bookId,
			"page":   pageParam,
		}).Errorf("requested with empty parameter")
		return c.NoContent(http.StatusBadRequest)
	}

	intPage, err := strconv.ParseInt(pageParam, 10, 0)
	if err != nil {
		c.Logger().WithFields(logrus.Fields{
			"bookId": bookId,
			"page":   pageParam,
		}).WithError(err).Errorf("requested with invalid page number")
		return c.NoContent(http.StatusBadRequest)
	}

	targetBook, bookExist := lo.Find(h.bookList, func(item Book) bool { // Use h.bookList
		return item.ID == BookId(bookId)
	})

	if !bookExist {
		c.Logger().WithFields(logrus.Fields{
			"bookId": bookId,
			"page":   pageParam,
		}).Errorf("requested book id not found") // Removed 'WithError(err)' as err is from ParseInt
		return c.NoContent(http.StatusNotFound)
	}

	if intPage < 1 || int(intPage) > targetBook.ImageCount {
		c.Logger().WithFields(logrus.Fields{
			"bookId": bookId,
			"page":   pageParam,
		}).Errorf("requested book page number is invalid") // Removed 'WithError(err)'
		return c.NoContent(http.StatusBadRequest)
	}

	if err := ImageViewerTemplate(targetBook, int(intPage)).Render(c.Response().Writer); err != nil {
		c.Logger().Error(err)
		return c.NoContent(http.StatusInternalServerError)
	}
	return nil
}

func (h *handlerEnv) handleImage(c echo.Context) error {
	path := c.Param("path")
	decodedPath, err := base64.StdEncoding.DecodeString(path)
	if err != nil {
		c.Logger().WithField("path", path).Errorf("requested with invalid base64 path")
		return c.NoContent(http.StatusBadRequest)
	}

	if h.fs == nil {
		c.Logger().Error("Filesystem in handlerEnv (h.fs) is not initialized for handleImage")
		return c.NoContent(http.StatusInternalServerError)
	}

	baseFs := afero.NewBasePathFs(h.fs, viper.GetString("ComicbookPath")) // Use h.fs
	stringPath := string(decodedPath)
	image, err := ImageFile(baseFs, stringPath)
	var fsErrDef *FileSystemError
	if err == nil {
		mimeType := mime.TypeByExtension(filepath.Ext(filepath.Base(stringPath)))
		c.Logger().WithFields(logrus.Fields{ // Using c.Logger()
			"path": stringPath,
			"mime": mimeType,
		}).Info("response image file") // Changed from Error to Info
		return c.Blob(http.StatusOK, mimeType, image)
	} else if errors.As(err, &fsErrDef) {
		c.Logger().WithError(err).WithField("path", stringPath).Error("failed to read requested image path, file system error")
		return c.NoContent(http.StatusInternalServerError)
	} else if errors.Is(err, ErrFileIsNotImage) {
		c.Logger().WithError(err).WithField("path", stringPath).Warn("requested image path doesn't direct image file, ignore") // Warn instead of Error
		return c.NoContent(http.StatusNotFound)
	} else if errors.Is(err, ErrFileNotFound) {
		c.Logger().WithError(err).WithField("path", stringPath).Warn("requested image path not found") // Warn instead of Error
		return c.NoContent(http.StatusNotFound)
	} else {
		c.Logger().WithError(err).WithField("path", stringPath).Error("failed to read requested image path, unknown image reading error")
		return c.NoContent(http.StatusInternalServerError)
	}
}
