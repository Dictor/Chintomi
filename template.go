package main

import (
	"encoding/base64"
	"fmt"

	g "github.com/maragudk/gomponents"
	hx "github.com/maragudk/gomponents-htmx"
	c "github.com/maragudk/gomponents/components"
	. "github.com/maragudk/gomponents/html"
	"github.com/samber/lo"
	"github.com/spf13/viper"
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
			Class("h-screen flex flex-col"),
			Div(Class("p-2 grow-0 shrink-0"),
				Div(Class("navbar bg-base-100 shadow-xl rounded-box"),
					A(
						Class("btn btn-ghost text-xl"),
						g.Text("Chintomi"),
						hx.Get(viper.GetString("UrlPrefix")+"/"), hx.Trigger("click"), hx.PushURL("true"), hx.Target("#content-area"),
					),
					Div(Class("form-control ml-auto"),
						Input(Class("input input-bordered w-24 md:w-auto"), Type("text"), Name("q"), Placeholder("검색"),
							hx.Get(viper.GetString("UrlPrefix")+"/"), hx.Trigger("keyup changed delay:500ms"), hx.Target("#content-area"), hx.PushURL("true"),
						),
					),
				)),
			Div(append([]g.Node{ID("content-area"), Class("grow shrink basis-full min-h-0 relative")}, content...)...),
		},
	})
}

func PageSelector(page int, totalPage int, limit int) g.Node {
	const maxPageButtons = 10
	var pages []int

	if totalPage <= maxPageButtons {
		pages = lo.RangeFrom(1, totalPage)
	} else {
		start := page - maxPageButtons/2
		if start < 1 {
			start = 1
		}
		end := start + maxPageButtons - 1
		if end > totalPage {
			end = totalPage
			start = end - maxPageButtons + 1
		}
		pages = lo.Range(end - start + 1)
		for i := range pages {
			pages[i] += start
		}

		if start > 1 {
			pages = append([]int{1, -1}, pages...)
		}
		if end < totalPage {
			pages = append(pages, -1, totalPage)
		}
	}

	return Div(Class("join"),
		g.Group(g.Map(pages, func(p int) g.Node {
			if p == -1 {
				return Button(Class("join-item btn btn-disabled"), g.Text("..."))
			}
			return Button(Class("join-item btn"), g.Textf("%d", p),
				hx.Get(fmt.Sprintf("%s/?page=%d&limit=%d", viper.GetString("UrlPrefix"), p, limit)),
				hx.Trigger("click"),
				hx.PushURL("true"),
				hx.Target("#content-area"),
			)
		})),
	)
}

func BookCardTemplate(books []Book, page int, totalPage int, limit int) []g.Node {
	list := g.Map(books, func(b Book) g.Node {
		thumbnailSrc := "https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"
		if b.HasThumbnail {
			thumbnailSrc = viper.GetString("UrlPrefix") + "/image/" + base64.URLEncoding.EncodeToString([]byte(b.ThumbnailFile))
		}

		return Div(Class("card bg-base-100 w-72 shadow-xl cursor-pointer"),
			hx.Get(fmt.Sprintf("%s/viewer/%s/%d", viper.GetString("UrlPrefix"), b.ID, 1)), hx.Trigger("click"), hx.PushURL("true"), hx.Target("#content-area"),
			Figure(Img(
				Src(thumbnailSrc),
				Alt("Thumbnail"),
			),
			),
			Div(Class("card-body p-5"),
				H2(Class("card-title"), g.Text(b.Name)),
				Ul(Class("list-none"),
					Li(g.Textf("%d 페이지", b.ImageCount)),
					Li(g.Textf("크기 %2.fMB", float32(b.ImageSize)/1000000)),
				),
			),
		)
	})

	pagination := Div(
		Class("flex justify-center items-center p-4"),
		PageSelector(page, totalPage, limit),
		Div(Class("dropdown dropdown-top"),
			Select(Name("limit"), Class("select select-bordered ml-2"),
				hx.Get(viper.GetString("UrlPrefix")+"/"), hx.Trigger("change"), hx.Target("#content-area"), hx.PushURL("true"),
				g.Group(g.Map([]int{10, 20, 50, 100}, func(l int) g.Node {
					return Option(g.If(l == limit, Selected()), Value(fmt.Sprintf("%d", l)), g.Textf("%d개씩 보기", l))
				})),
			),
		),
	)

	return []g.Node{
		pagination,
		Div(Class("grow p-2 flex flex-row flex-wrap justify-around gap-y-4"),
			g.Group(list),
		),
		pagination,
	}
}

func ImageViewerTemplate(book Book, page int) []g.Node {
	imgPath := viper.GetString("UrlPrefix") + "/image/" + base64.StdEncoding.EncodeToString([]byte(book.ImageFiles[page-1]))
	imageProperty := []g.Node{
		Src(imgPath),
		Alt("page"),
		Class("object-contain max-w-full max-h-full m-auto"),
	}
	if page < book.ImageCount {
		imageProperty = append(
			imageProperty,
			hx.Get(fmt.Sprintf("%s/viewer/%s/%d", viper.GetString("UrlPrefix"), book.ID, page+1)), hx.Trigger("click"), hx.PushURL("true"), hx.Target("#content-area"),
		)
	}

	return []g.Node{
		Img(imageProperty...),
		Div(
			Class("absolute top-0 left-0 m-5"),
			g.Textf("%d / %d", page, book.ImageCount),
		),
	}
}
