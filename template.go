package main

import (
	g "github.com/maragudk/gomponents"
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
		},
		Body: []g.Node{
			Class("flex flex-col"),
			Div(Class("grow p-2"),
				Div(Class("navbar bg-base-100 shadow-xl rounded-box"),
					A(Class("btn btn-ghost text-xl"), g.Text("Chintomi")),
				)),
			Div(append([]g.Node{Class("grow p-2 flex flex-row flex-wrap justify-around")}, content...)...),
		},
	})
}

func BookCardTemplate(books []Book) []g.Node {
	return g.Map(books, func(b Book) g.Node {
		return Div(Class("card bg-base-100 w-72 shadow-xl"),
			Figure(Img(
				Src("https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"),
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
					Button(Class("btn btn-primary"), g.Text("Button")),
				),
			),
		)
	})
}
