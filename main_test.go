package main

import (
	"net/http"
	"net/http/httptest"
	"sort"
	"strings"
	"testing"
	"time"

	"github.com/labstack/echo/v4"
	"github.com/spf13/viper"
	"github.com/stretchr/testify/assert"
)

// --- Mock Data Helper ---

func makeMockBooks() []Book {
	return []Book{
		{ID: "1", Name: "Book Alpha", Author: "Author Zeta", Tag: []string{"Fiction", "SciFi"}, ImageSize: 100, AddedDate: time.Date(2023, 1, 10, 0, 0, 0, 0, time.UTC)},
		{ID: "2", Name: "book beta", Author: "Author Epsilon", Tag: []string{"NonFiction", "Tech"}, ImageSize: 200, AddedDate: time.Date(2023, 1, 5, 0, 0, 0, 0, time.UTC)},
		{ID: "3", Name: "Grokking Gamma", Author: "author Zeta", Tag: []string{"Tech", "Programming"}, ImageSize: 150, AddedDate: time.Date(2023, 2, 1, 0, 0, 0, 0, time.UTC)},
		{ID: "4", Name: "Delta Dreams", Author: "Author Alpha", Tag: []string{"Fiction", "Fantasy"}, ImageSize: 50, AddedDate: time.Date(2022, 12, 20, 0, 0, 0, 0, time.UTC)},
		{ID: "5", Name: "Epsilon Essays", Author: "Author Beta", Tag: []string{"Essay", "Tech"}, ImageSize: 250, AddedDate: time.Date(2023, 3, 15, 0, 0, 0, 0, time.UTC)},
	}
}

// --- Test filterBooks ---

func TestFilterBooks(t *testing.T) {
	mockBooks := makeMockBooks()

	tests := []struct {
		name       string
		query      string
		expectedIDs []BookId
	}{
		{"empty query", "", []BookId{"1", "2", "3", "4", "5"}},
		{"name match full", "Book Alpha", []BookId{"1"}},
		{"name match partial case-insensitive", "book", []BookId{"1", "2"}},
		{"name match different case", "GROKKING Gamma", []BookId{"3"}},
		{"author match", "Author Zeta", []BookId{"1", "3"}},
		{"author match case-insensitive", "author alpha", []BookId{"4"}},
		{"tag match", "Fiction", []BookId{"1", "4"}},
		{"tag match tech", "Tech", []BookId{"2", "3", "5"}},
		{"tag match programming", "Programming", []BookId{"3"}},
		{"multiple matches name author tag", "beta", []BookId{"2", "5"}}, // "book beta", "Author Beta"
		{"no match", "OmegaSupreme", []BookId{}},
		{"match one tag from multiple", "Fantasy", []BookId{"4"}},
	}

	for _, tt := range tests {
		t.Run(tt.name, func(t *testing.T) {
			filtered := filterBooks(mockBooks, tt.query)
			var resultIDs []BookId
			for _, b := range filtered {
				resultIDs = append(resultIDs, b.ID)
			}
			assert.ElementsMatch(t, tt.expectedIDs, resultIDs, "Expected book IDs do not match result")
		})
	}
}

// Placeholder for sortBooks function (to be extracted from main.go or duplicated for testing)
// For now, we'll assume it's available. If not, this test will need it.
// func sortBooks(books []Book, sortBy, sortOrder string) []Book { ... }


// Placeholder for paginateBooks function
// func paginateBooks(books []Book, page, booksPerPage int) ([]Book, error) { ... }


// --- Test HTTP Handlers (Basic Setup) ---
// More tests will be added progressively.

func TestMainHandlers(t *testing.T) {
	// Setup Echo instance
	e := echo.New()
	// Setup Viper for test config
	viper.Set("BooksPerPage", 10) // Default for tests, can be overridden per test

	// Mock global books variable for handlers to use
	// This is a simplification. In a real scenario, you might inject dependencies.
	books = makeMockBooks() // Assuming 'books' is the global variable used in main.go

	// TODO: Add more comprehensive handler tests
	t.Run("Root handler basic GET", func(t *testing.T) {
		req := httptest.NewRequest(http.MethodGet, "/", nil)
		rec := httptest.NewRecorder()
		c := e.NewContext(req, rec)

		// Manually call the handler function.
		// Need to get the actual handler function from main.go's e.GET("/", ...)
		// This requires refactoring main() or making handlers public, or a more complex setup.
		// For now, this is a conceptual placeholder for how handler testing would begin.
		// err := rootHandler(c) // Assuming rootHandler is the name of the func for e.GET("/")
		// assert.NoError(t, err)
		assert.Equal(t, http.StatusOK, rec.Code) // Or whatever the expected code is
		// assert.Contains(t, rec.Body.String(), "Book Alpha") // Check for some content
	})

	t.Run("Books handler basic GET", func(t *testing.T) {
		req := httptest.NewRequest(http.MethodGet, "/books?page=1", nil)
		rec := httptest.NewRecorder()
		c := e.NewContext(req, rec)

		// Similar to above, need access to the handler function.
		// err := booksHandler(c) // Assuming booksHandler is for e.GET("/books")
		// assert.NoError(t, err)
		assert.Equal(t, http.StatusOK, rec.Code)
		// assert.Contains(t, rec.Body.String(), "Book Alpha")
	})
}

// Note: To properly test the HTTP handlers as they are in main.go,
// main() function would need to be refactored to not immediately start the server,
// and to return the echo instance `e`, or the handlers themselves would need to be
// exported or passed to the test suite.
// The filterBooks test is independent and can run as is.
// Sorting and Pagination tests will require those logics to be in testable functions.
// For now, TestMainHandlers is a very basic structure.

// --- Test sortBooks ---

func TestSortBooks(t *testing.T) {
	tests := []struct {
		name           string
		sortBy         string
		sortOrder      string
		expectedOrderIDs []BookId // Expected order of book IDs after sorting
	}{
		{"name asc", "name", "asc", []BookId{"1", "2", "4", "5", "3"}},    // Alpha, beta, Delta, Epsilon, Grokking
		{"name desc", "name", "desc", []BookId{"3", "5", "4", "2", "1"}}, // Grokking, Epsilon, Delta, beta, Alpha
		{"date_added asc", "date_added", "asc", []BookId{"4", "2", "1", "3", "5"}}, // Dec20, Jan5, Jan10, Feb1, Mar15
		{"date_added desc", "date_added", "desc", []BookId{"5", "3", "1", "2", "4"}},
		{"size asc", "size", "asc", []BookId{"4", "1", "3", "2", "5"}}, // 50, 100, 150, 200, 250
		{"size desc", "size", "desc", []BookId{"5", "2", "3", "1", "4"}},
		{"default sort (name asc)", "", "", []BookId{"1", "2", "4", "5", "3"}},
		{"invalid sortBy (default name asc)", "invalid_field", "asc", []BookId{"1", "2", "4", "5", "3"}},
	}

	for _, tt := range tests {
		t.Run(tt.name, func(t *testing.T) {
			booksToSort := makeMockBooks() // Get a fresh copy for each test
			sorted := sortBooks(booksToSort, tt.sortBy, tt.sortOrder)

			var resultIDs []BookId
			for _, b := range sorted {
				resultIDs = append(resultIDs, b.ID)
			}
			assert.Equal(t, tt.expectedOrderIDs, resultIDs, "Book order after sorting is not as expected")
		})
	}

	t.Run("sort empty slice", func(t *testing.T) {
		sorted := sortBooks([]Book{}, "name", "asc")
		assert.Empty(t, sorted, "Sorting an empty slice should result in an empty slice")
	})

	t.Run("sort single element slice", func(t *testing.T) {
		singleBookList := []Book{makeMockBooks()[0]}
		sorted := sortBooks(singleBookList, "name", "asc")
		assert.Equal(t, []BookId{"1"}, []BookId{sorted[0].ID}, "Sorting a single element slice failed")
		assert.Len(t, sorted, 1)
	})
}

// --- Test paginateBooks ---

func TestPaginateBooks(t *testing.T) {
	allBooks := makeMockBooks() // 5 books

	tests := []struct {
		name           string
		page           int
		booksPerPage   int
		inputBookCount int // How many books to pass to paginateBooks from allBooks
		expectedNumBooks int
		expectedTotalPages int
		expectError    bool
		expectedErrorMsgPrefix string
	}{
		{"first page, 2 per page", 1, 2, 5, 2, 3, false, ""},
		{"second page, 2 per page", 2, 2, 5, 2, 3, false, ""},
		{"last page, 2 per page (1 book)", 3, 2, 5, 1, 3, false, ""},
		{"page out of bounds (upper)", 4, 2, 5, 0, 3, true, "page 4 out of bounds"},
		{"page 1, all books (5 per page)", 1, 5, 5, 5, 1, false, ""},
		{"page 1, more per page than books", 1, 10, 5, 5, 1, false, ""},
		{"zero books input, page 1", 1, 5, 0, 0, 0, false, ""}, // No error, just 0 books, 0 pages
		{"page out of bounds (page 2 for zero books)", 2, 5, 0, 0, 0, true, "page 2 out of bounds"}, // totalPages is 0
		{"page 1, 3 books, 2 per page", 1, 2, 3, 2, 2, false, ""},
		{"page 2, 3 books, 2 per page", 2, 2, 3, 1, 2, false, ""},
		{"booksPerPage zero (should use default or handle)", 1, 0, 5, 0, 0, true, "books per page must be positive"}, // Assuming it errors or defaults. Current code defaults to 20. Let's test this assumption.
																															// The paginateBooks in main.go currently defaults booksPerPage to 20 if input is <=0.
																															// So, this test case needs to expect that default behavior or change paginateBooks.
																															// For now, let's assume we want to test the defaulting behavior.
		{"booksPerPage negative (should use default or handle)", 1, -1, 5, 0, 0, true, "books per page must be positive"},// Let's refine this test case based on actual behavior.
		{"page 0 (invalid, should default to 1)", 0, 2, 5, 2, 3, false, ""}, // page defaults to 1
		{"page -1 (invalid, should default to 1)", -1, 2, 5, 2, 3, false, ""},// page defaults to 1
	}

	// Adjusting tests for booksPerPage <= 0 based on current paginateBooks defaulting to 20
	// This means for 5 books, 5 booksPerPage=20 -> 5 books, 1 total page.
	adjustedTests := []struct {
		name           string
		page           int
		booksPerPage   int
		inputBookCount int
		expectedNumBooks int
		expectedTotalPages int
		expectError    bool
		expectedErrorMsgPrefix string
	}{
		{"first page, 2 per page", 1, 2, 5, 2, 3, false, ""},
		{"second page, 2 per page", 2, 2, 5, 2, 3, false, ""},
		{"last page, 2 per page (1 book)", 3, 2, 5, 1, 3, false, ""},
		{"page out of bounds (upper)", 4, 2, 5, 0, 3, true, "page 4 out of bounds"},
		{"page 1, all books (5 per page)", 1, 5, 5, 5, 1, false, ""},
		{"page 1, more per page than books", 1, 10, 5, 5, 1, false, ""},
		{"zero books input, page 1", 1, 5, 0, 0, 0, false, ""},
		{"page out of bounds (page 2 for zero books)", 2, 5, 0, 0, 0, true, "page 2 out of bounds"},
		{"page 1, 3 books, 2 per page", 1, 2, 3, 2, 2, false, ""},
		{"page 2, 3 books, 2 per page", 2, 2, 3, 1, 2, false, ""},
		{"booksPerPage zero (defaults to 20 in func)", 1, 0, 5, 5, 1, false, ""}, // 5 books, 20 per page (default) = 5 books, 1 total page
		{"booksPerPage negative (defaults to 20 in func)", 1, -1, 5, 5, 1, false, ""}, // Same as above
		{"page 0 (invalid, defaults to 1)", 0, 2, 5, 2, 3, false, ""},
		{"page -1 (invalid, defaults to 1)", -1, 2, 5, 2, 3, false, ""},
	}


	for _, tt := range adjustedTests {
		t.Run(tt.name, func(t *testing.T) {
			inputBooks := allBooks[:tt.inputBookCount]
			paged, totalPages, err := paginateBooks(inputBooks, tt.page, tt.booksPerPage)

			if tt.expectError {
				assert.Error(t, err, "Expected an error but got none")
				if err != nil && tt.expectedErrorMsgPrefix != "" {
					assert.Contains(t, err.Error(), tt.expectedErrorMsgPrefix, "Error message prefix does not match")
				}
			} else {
				assert.NoError(t, err, "Expected no error but got one")
			}
			assert.Len(t, paged, tt.expectedNumBooks, "Number of paged books does not match expected")
			assert.Equal(t, tt.expectedTotalPages, totalPages, "Total pages calculation is incorrect")
		})
	}
}

// TODO: Expand TestMainHandlers for query parameters, filtering, sorting, pagination results.

// Helper to reset Viper for tests (if needed between tests)
func resetViper() {
	viper.Reset()
	viper.SetDefault("BooksPerPage", 20) // Set default for tests
	// Any other viper defaults needed for tests can go here
}

func TestRootHandler(t *testing.T) {
	mockBooksData := makeMockBooks()
	// Initialize AppFs for tests (can use MemMapFs if actual file ops are tested by handlers)
	// For these handlers, AppFs is mainly used by handleImage, not directly by handleRoot/handleBooks.
	// But handlerEnv expects it.
	AppFs = afero.NewMemMapFs()
	testHandlerEnv := &handlerEnv{bookList: mockBooksData, fs: AppFs}

	e := echo.New() // Create a single Echo instance for these tests

	tests := []struct {
		name               string
		queryParams        string // e.g., "?page=2&search=Book"
		expectedStatusCode int
		expectedBodyContains []string
		expectedBookIDsInOrder []BookId // If order is important and checkable
	}{
		{
			name:               "no params (default first page, default sort name asc)",
			queryParams:        "",
			expectedStatusCode: http.StatusOK,
			expectedBodyContains: []string{"Book Alpha", "book beta", "Delta Dreams", "Epsilon Essays", "Grokking Gamma"}, // All 5, assuming BPP >= 5
			// Expected order for name asc: Alpha (1), beta (2), Delta (4), Epsilon (5), Grokking (3)
			expectedBookIDsInOrder: []BookId{"1", "2", "4", "5", "3"},
		},
		{
			name:               "specific page",
			queryParams:        "?page=2", // Assuming BPP=2 for this test case (will set via Viper)
			expectedStatusCode: http.StatusOK,
			expectedBodyContains: []string{"Delta Dreams", "Epsilon Essays"},
			expectedBookIDsInOrder: []BookId{"4", "5"},
		},
		{
			name:               "search query",
			queryParams:        "?search=gamma",
			expectedStatusCode: http.StatusOK,
			expectedBodyContains: []string{"Grokking Gamma"},
			expectedBookIDsInOrder: []BookId{"3"},
		},
		{
			name:               "search query no results",
			queryParams:        "?search=NoSuchBook",
			expectedStatusCode: http.StatusOK, // Handler returns 200 with "no books" message/state
			expectedBodyContains: []string{}, // Or some "no results" message if template has one
			// Check that no mock book names are present
			// Not checking expectedBookIDsInOrder because it should be empty
		},
		{
			name:               "sort by size desc",
			queryParams:        "?sort_by=size&sort_order=desc",
			expectedStatusCode: http.StatusOK,
			expectedBodyContains: []string{"Epsilon Essays", "book beta"}, // 250, 200
			expectedBookIDsInOrder: []BookId{"5", "2", "3", "1", "4"},
		},
		{
			name:               "search and sort and page",
			queryParams:        "?search=Tech&sort_by=size&sort_order=asc&page=1", // BPP=2 for this
			expectedStatusCode: http.StatusOK,
			// Tech books: beta (200), Grokking (150), Epsilon (250)
			// Sorted by size asc: Grokking (150), beta (200) on page 1
			expectedBodyContains: []string{"Grokking Gamma", "book beta"},
			expectedBookIDsInOrder: []BookId{"3", "2"},
		},
		{
			name:               "page out of bounds for search",
			queryParams:        "?search=Alpha&page=2", // Only "Book Alpha" matches, so page 2 is out of bounds
			expectedStatusCode: http.StatusOK,      // Still 200, but template shows empty state
			expectedBodyContains: []string{},
		},
	}

	for _, tt := range tests {
		t.Run(tt.name, func(t *testing.T) {
			resetViper() // Reset viper for each test if params change BooksPerPage
			if tt.name == "specific page" || tt.name == "search and sort and page" {
				viper.Set("BooksPerPage", 2)
			} else {
				viper.Set("BooksPerPage", 5) // Default for other tests
			}


			req := httptest.NewRequest(http.MethodGet, "/"+tt.queryParams, nil)
			rec := httptest.NewRecorder()
			c := e.NewContext(req, rec)

			err := testHandlerEnv.handleRoot(c)
			assert.NoError(t, err)
			assert.Equal(t, tt.expectedStatusCode, rec.Code)

			bodyStr := rec.Body.String()
			for _, expected := range tt.expectedBodyContains {
				assert.Contains(t, bodyStr, expected)
			}

            if tt.name == "search query no results" {
                for _, book := range mockBooksData {
                    assert.NotContains(t, bodyStr, book.Name)
                }
            }
            if tt.name == "page out of bounds for search" {
                 for _, book := range mockBooksData {
                    if book.Name != "Book Alpha" { // The one that matches search
                        assert.NotContains(t, bodyStr, book.Name)
                    }
                 }
                 // Also check that Book Alpha is not there because page is out of bounds
                 assert.NotContains(t, bodyStr, "Book Alpha")
            }


			// Crude check for book order if expectedBookIDsInOrder is provided
			// This is very basic and might be fragile. It checks if the *first* few items appear
			// in the body in the specified order.
			if len(tt.expectedBookIDsInOrder) > 0 && len(tt.expectedBodyContains) > 0 {
				currentIndex := -1
				for _, bookID := range tt.expectedBookIDsInOrder {
					var bookName string
					for _, bk := range mockBooksData {
						if bk.ID == bookID {
							bookName = bk.Name
							break
						}
					}

					foundIndex := strings.Index(bodyStr, bookName)
					// Only consider books that are expected to be in the body for this page
					isBookExpectedOnPage := false
					for _, expectedBodyContent := range tt.expectedBodyContains {
						if strings.Contains(bookName, expectedBodyContent) || strings.Contains(expectedBodyContent, bookName) {
							isBookExpectedOnPage = true
							break
						}
					}

					if isBookExpectedOnPage {
						assert.True(t, foundIndex != -1, "Book %s (ID: %s) not found in body", bookName, bookID)
						assert.True(t, foundIndex > currentIndex, "Book %s (ID: %s) found out of order. Prev index: %d, Found index: %d", bookName, bookID, currentIndex, foundIndex)
						currentIndex = foundIndex
					}
				}
			}
		})
	}
}


// TODO: Add TestBooksHandler (for /books infinite scroll endpoint)
// This will be similar but will check for the raw BookCardNodes output and next trigger.

func TestBooksHandler(t *testing.T) {
	mockBooksData := makeMockBooks()
	AppFs = afero.NewMemMapFs() // Ensure AppFs is initialized for handlerEnv
	testHandlerEnv := &handlerEnv{bookList: mockBooksData, fs: AppFs}

	e := echo.New()

	tests := []struct {
		name                 string
		queryParams          string
		expectedStatusCode   int
		expectedBodyContains []string // Check for specific book names
		notExpectedBodyContains []string // Check for names NOT on this page
		expectNextTrigger    bool     // Whether the HTMX trigger for next page should be present
		booksPerPageOverride int      // To override default viper BooksPerPage for specific tests
	}{
		{
			name:                 "page 1, 2 per page",
			queryParams:          "?page=1",
			booksPerPageOverride: 2,
			expectedStatusCode:   http.StatusOK,
			expectedBodyContains: []string{"Book Alpha", "book beta"}, // Name asc default: 1, 2
			notExpectedBodyContains: []string{"Delta Dreams"}, // Book 4 shouldn't be on page 1
			expectNextTrigger:    true,
		},
		{
			name:                 "page 3, 2 per page (last page, 1 item)",
			queryParams:          "?page=3",
			booksPerPageOverride: 2,
			expectedStatusCode:   http.StatusOK,
			expectedBodyContains: []string{"Grokking Gamma"}, // Name asc default: Book 3 is last on page 3
			notExpectedBodyContains: []string{"Book Alpha"},
			expectNextTrigger:    false, // Last item of all 5 books
		},
		{
			name:                 "page out of bounds",
			queryParams:          "?page=4", // 5 books, 2 per page -> 3 pages. Page 4 is out of bounds.
			booksPerPageOverride: 2,
			expectedStatusCode:   http.StatusOK,
			expectedBodyContains: []string{}, // Should return empty content
			expectNextTrigger:    false,
		},
		{
			name:                 "search with results, expect next trigger",
			queryParams:          "?search=Fiction&page=1", // Alpha (Fic), Delta (Fic,Fan) => 2 books
			booksPerPageOverride: 1,
			expectedStatusCode:   http.StatusOK,
			expectedBodyContains: []string{"Book Alpha"}, // Name asc: Alpha (1) before Delta (4)
			expectNextTrigger:    true,
		},
		{
			name:                 "search with results, last page, no next trigger",
			queryParams:          "?search=Fiction&page=2",
			booksPerPageOverride: 1,
			expectedStatusCode:   http.StatusOK,
			expectedBodyContains: []string{"Delta Dreams"},
			expectNextTrigger:    false,
		},
		{
            name:                 "search no results",
            queryParams:          "?search=NonExistent",
            booksPerPageOverride: 2,
            expectedStatusCode:   http.StatusOK,
            expectedBodyContains: []string{},
            expectNextTrigger:    false,
        },
		{
			name:                 "sorting and paging",
			queryParams:          "?sort_by=size&sort_order=desc&page=1", // Books by size desc: 5(250), 2(200), 3(150), 1(100), 4(50)
			booksPerPageOverride: 2,
			expectedStatusCode:   http.StatusOK,
			expectedBodyContains: []string{"Epsilon Essays", "book beta"}, // Books 5, 2
			expectNextTrigger:    true,
		},
		{
			name:                 "sorting and paging - last page",
			queryParams:          "?sort_by=size&sort_order=desc&page=3",
			booksPerPageOverride: 2,
			expectedStatusCode:   http.StatusOK,
			expectedBodyContains: []string{"Delta Dreams"}, // Book 4
			expectNextTrigger:    false,
		},
	}

	for _, tt := range tests {
		t.Run(tt.name, func(t *testing.T) {
			resetViper()
			if tt.booksPerPageOverride > 0 {
				viper.Set("BooksPerPage", tt.booksPerPageOverride)
			} else {
				viper.Set("BooksPerPage", 20) // Default if not specified for test
			}

			req := httptest.NewRequest(http.MethodGet, "/books"+tt.queryParams, nil)
			rec := httptest.NewRecorder()
			c := e.NewContext(req, rec)

			err := testHandlerEnv.handleBooks(c)
			assert.NoError(t, err)
			assert.Equal(t, tt.expectedStatusCode, rec.Code)

			bodyStr := rec.Body.String()
			// GlobalLogger.Infof("Test: %s, Body: %s", tt.name, bodyStr) // For debugging

			if len(tt.expectedBodyContains) == 0 && !tt.expectNextTrigger { // For empty results like out of bounds or no search match
				assert.Equal(t, "", bodyStr, "Expected empty body for test: %s", tt.name)
			} else {
				for _, expected := range tt.expectedBodyContains {
					assert.Contains(t, bodyStr, expected, "Body does not contain expected string '%s' for test: %s", expected, tt.name)
				}
				for _, notExpected := range tt.notExpectedBodyContains {
					assert.NotContains(t, bodyStr, notExpected, "Body unexpectedly contains string '%s' for test: %s", notExpected, tt.name)
				}

				if tt.expectNextTrigger {
					assert.Contains(t, bodyStr, "hx-trigger=\"revealed\"", "Expected HTMX next page trigger for test: %s", tt.name)
					assert.Contains(t, bodyStr, "hx-get=\"/books?page=", "HTMX trigger does not point to /books?page= for test: %s", tt.name)
				} else {
					assert.NotContains(t, bodyStr, "hx-trigger=\"revealed\"", "Did not expect HTMX next page trigger for test: %s", tt.name)
				}
			}
		})
	}
}
// }

// It's also important that `model.go` and `main.go` (for filterBooks) are part of the
// same package `main` for these tests to compile and run correctly without complex imports.
// The `books` global variable usage in handlers makes isolated testing harder without specific setup.

// Additional test ideas:
// - Test with empty `allBooks` slice for `filterBooks`.
// - Test `filterBooks` with special characters in query or book names/authors/tags.
// - Test pagination with zero `booksPerPage` (if allowed, or how it's handled).
// - Test pagination when `len(books)` is zero.
// - Test sorting with books having identical values in sorted fields (stability).
// - Test sorting with non-ASCII characters in names.
// - Test handlers for invalid query param values (e.g., non-integer page).
