package main

import (
	"encoding/base64"
	"fmt"

	g "github.com/maragudk/gomponents"
	hx "github.com/maragudk/gomponents-htmx"
	c "github.com/maragudk/gomponents/components"
	. "github.com/maragudk/gomponents/html"
)

func BaseTemplate(content ...g.Node) g.Node {
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
					A(Class("btn btn-ghost text-xl"), g.Text("Chintomi")),
				)),
			Div(append([]g.Node{ID("content-area")}, content...)...),
		},
	})
}

func BookCardTemplate(books []Book) g.Node {
	list := g.Map(books, func(b Book) g.Node {
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
	})
	return Div(append([]g.Node{Class("grow p-2 flex flex-row flex-wrap justify-around")}, list...)...)
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
