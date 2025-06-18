package main

import (
	"encoding/base64"
	"fmt"

	g "github.com/maragudk/gomponents"
	hx "github.com/maragudk/gomponents-htmx"
	c "github.com/maragudk/gomponents/components"
	. "github.com/maragudk/gomponents/html"
	"github.com/samber/lo" // Added for lo.Ternary
)

func BaseTemplate(currentSearchQuery string, content ...g.Node) g.Node {
	return c.HTML5(c.HTML5Props{
		Title:    "Chintomi",
		Language: "ko",
		Head: []g.Node{
			Meta(Charset("utf-8")),
			Meta(Name("viewport"), Content("width=device-width, initial-scale=1")),
			Link(Href("https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css"), Rel("stylesheet"), Type("text/css")),
			Script(Src("https://cdn.tailwindcss.com")),
			Script(Src("https://unpkg.com/htmx.org@2.0.2")),
		},
		Body: []g.Node{
			Class("flex flex-col"),
			Div(Class("grow p-2"),
				Div(Class("navbar bg-base-100 shadow-xl rounded-box"),
					A(Class("btn btn-ghost text-xl"), Href("/"), g.Text("Chintomi")), // Added Href to reset to homepage
					Div(Class("flex-1 justify-center px-2"), // Centering search bar a bit
						Input(
							Type("search"),
							Name("search"),
							Class("input input-bordered w-full max-w-xs"), // DaisyUI classes
							Placeholder("Search by name, author, tag..."),
							Value(currentSearchQuery),
							hx.Get("/"), // Submit to the root path, which handles book listing
							hx.Trigger("keyup changed delay:500ms, search"),
							hx.Target("#content-area"),      // Target the div that wraps BookCardTemplate
							hx.Indicator("#search-indicator"), // Specify an indicator
							hx.PushURL("true"),                // Update browser URL
						),
						Span(ID("search-indicator"), Class("loading loading-spinner loading-sm ml-2 htmx-indicator")), // DaisyUI spinner
					),
					// Placeholder for potential user/auth section if navbar grows
					Div(Class("flex-none")),
				)),
			Div(append([]g.Node{ID("content-area")}, content...)...),
		},
	})
}

// BookCardNodes generates only the list of book card nodes.
func BookCardNodes(books []Book) g.Node {
	return g.Group(g.Map(books, func(b Book) g.Node {
		thumbnailSrc := "https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"
		if b.HasThumbnail {
			thumbnailSrc = "/image/" + base64.URLEncoding.EncodeToString([]byte(b.ThumbnailFile))
		}

		return Div(Class("card bg-base-100 w-72 shadow-xl"),
			Figure(Img(
				Src(thumbnailSrc),
				Alt("Thumbnail"),
			),
			),
			Div(Class("card-body"),
				H2(Class("card-title"), g.Text(b.Name)),
				Ul(Class("list-none"),
					Li(g.Textf("%d Page", b.ImageCount)),
					Li(g.Textf("Size: %d", b.ImageSize)),
				),
				Div(Class("card-actions justify-end"),
					Button(Class("btn btn-primary"), hx.Get(fmt.Sprintf("/viewer/%s/%d", b.ID, 1)), hx.Trigger("click"), hx.PushURL("true"), hx.Target("#content-area"), g.Text("열기")),
					Button(Class("btn btn-primary"), g.Text("관리")),
				),
			),
		)
	}))
}

func BookCardTemplate(books []Book, currentPage, totalPages, prevPage, nextPage int, hasPrevPage, hasNextPage bool, currentSearchQuery string, currentSortBy string, currentSortOrder string) g.Node {
	// Helper function to generate sort URL
	sortURL := func(sortBy, sortOrder string) string {
		url := fmt.Sprintf("/?sort_by=%s&sort_order=%s&page=1", sortBy, sortOrder) // Reset to page 1 on sort change
		if currentSearchQuery != "" {
			url += "&search=" + currentSearchQuery
		}
		return url
	}

	// Helper function to determine if a sort button is active
	isSortActive := func(sortBy, sortOrder string) bool {
		return currentSortBy == sortBy && currentSortOrder == sortOrder
	}

	// Convert int/bool to string for g.Attr and g.If conditions as they expect strings or g.Node
	currentPageStr := fmt.Sprintf("%d", currentPage)
	totalPagesStr := fmt.Sprintf("%d", totalPages)
	prevPageStr := fmt.Sprintf("%d", prevPage)
	nextPageStr := fmt.Sprintf("%d", nextPage)
	// hasPrevPage for g.If condition does not need to be string
	// hasNextPage for g.If condition does not need to be string

	// Generate book card nodes using the new function
	bookNodes := BookCardNodes(books)

	// Container for book cards and the infinite scroll trigger
	bookListWithScroll := []g.Node{
		Class("flex flex-row flex-wrap justify-around"), // Class for the wrapper of book cards
		bookNodes,
	}

	// Add infinite scroll trigger div if there is a next page
	if hasNextPage {
		nextPageStrForTrigger := fmt.Sprintf("%d", nextPage)
		loadMoreURL := fmt.Sprintf("/books?page=%s", nextPageStrForTrigger)
		if currentSearchQuery != "" {
			loadMoreURL += "&search=" + currentSearchQuery
		}
		if currentSortBy != "" && currentSortOrder != "" {
			loadMoreURL += "&sort_by=" + currentSortBy + "&sort_order=" + currentSortOrder
		}
		bookListWithScroll = append(bookListWithScroll,
			Div(
				hx.Get(loadMoreURL),
				hx.Trigger("revealed"),
				hx.Swap("outerHTML"),
				// You might want to add some visual cue or placeholder class here
				Class("w-full h-10"), // Example: a placeholder to trigger reveal
			),
		)
	}

	return Div(
		Class("grow p-2 flex flex-col"),
		// Sort Controls
		Div(Class("p-4 flex flex-wrap justify-center items-center gap-2 bg-base-200 rounded-box mb-4"),
			Span(Class("font-semibold mr-2"), g.Text("Sort by:")),
			Div(Class("join"),
				Button(
					Class("join-item btn btn-sm"+lo.Ternary(isSortActive("name", "asc"), " btn-active", "")),
					hx.Get(sortURL("name", "asc")), hx.Target("#content-area"), hx.PushURL("true"),
					hx.Include("[name='search'], [name='page']"), // page is reset, but search is kept
					g.Text("Name ↑"),
				),
				Button(
					Class("join-item btn btn-sm"+lo.Ternary(isSortActive("name", "desc"), " btn-active", "")),
					hx.Get(sortURL("name", "desc")), hx.Target("#content-area"), hx.PushURL("true"),
					hx.Include("[name='search'], [name='page']"),
					g.Text("Name ↓"),
				),
			),
			Div(Class("join"),
				Button(
					Class("join-item btn btn-sm"+lo.Ternary(isSortActive("date_added", "asc"), " btn-active", "")),
					hx.Get(sortURL("date_added", "asc")), hx.Target("#content-area"), hx.PushURL("true"),
					hx.Include("[name='search'], [name='page']"),
					g.Text("Date ↑"),
				),
				Button(
					Class("join-item btn btn-sm"+lo.Ternary(isSortActive("date_added", "desc"), " btn-active", "")),
					hx.Get(sortURL("date_added", "desc")), hx.Target("#content-area"), hx.PushURL("true"),
					hx.Include("[name='search'], [name='page']"),
					g.Text("Date ↓"),
				),
			),
			Div(Class("join"),
				Button(
					Class("join-item btn btn-sm"+lo.Ternary(isSortActive("size", "asc"), " btn-active", "")),
					hx.Get(sortURL("size", "asc")), hx.Target("#content-area"), hx.PushURL("true"),
					hx.Include("[name='search'], [name='page']"),
					g.Text("Size ↑"),
				),
				Button(
					Class("join-item btn btn-sm"+lo.Ternary(isSortActive("size", "desc"), " btn-active", "")),
					hx.Get(sortURL("size", "desc")), hx.Target("#content-area"), hx.PushURL("true"),
					hx.Include("[name='search'], [name='page']"),
					g.Text("Size ↓"),
				),
			),
		),
		Div(bookListWithScroll...), // Add the list of books and potentially the scroll trigger
		// Pagination Controls
		Div(
			Class("join p-4 self-center"),
			g.If(hasPrevPage,
				Button(
					Class("join-item btn"),
					hx.Get(fmt.Sprintf("/?page=%s%s%s", prevPageStr,
						lo.Ternary(currentSearchQuery != "", "&search="+currentSearchQuery, ""),
						lo.Ternary(currentSortBy != "" && currentSortOrder != "", "&sort_by="+currentSortBy+"&sort_order="+currentSortOrder, ""),
					)),
					hx.Target("#content-area"),
					hx.PushURL("true"),
					g.Text("« Previous"),
				),
			),
			Button(Class("join-item btn"), g.Textf("Page %s / %s", currentPageStr, totalPagesStr)),
			g.If(hasNextPage,
				Button(
					Class("join-item btn"),
					hx.Get(fmt.Sprintf("/?page=%s%s%s", nextPageStr,
						lo.Ternary(currentSearchQuery != "", "&search="+currentSearchQuery, ""),
						lo.Ternary(currentSortBy != "" && currentSortOrder != "", "&sort_by="+currentSortBy+"&sort_order="+currentSortOrder, ""),
					)),
					hx.Target("#content-area"),
					hx.PushURL("true"),
					g.Text("Next »"),
				),
			),
		),
	)
}

func ImageViewerTemplate(book Book, page int) g.Node {
	imgPath := "/image/" + base64.StdEncoding.EncodeToString([]byte(book.ImageFiles[page-1]))
	return Div(
		Img(
			Src(imgPath),
			Alt("page"),
			Class("max-h-full"),
		),
		Div(
			Class("absolute bottom-0 left-0")
		)
	)
}
