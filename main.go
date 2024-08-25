package main

import (
	"fmt"
	"net/http"
	"time"

	elogrus "github.com/dictor/echologrus"
	"github.com/labstack/echo/v4"
)

func main() {
	e := echo.New()
	elogrus.Attach(e)
	e.GET("/", func(c echo.Context) error {
		books := []Book{}

		for i := 0; i < 50; i++ {
			books = append(books, Book{
				ID:         "",
				Name:       fmt.Sprintf("%d", i),
				Path:       "",
				Author:     "",
				ImageCount: i,
				ImageSize:  i,
				AddedDate:  time.Time{},
			})
		}

		if err := BaseTemplate(BookCardTemplate(books)...).Render(c.Response().Writer); err != nil {
			e.Logger.Error(err)
			return c.NoContent(http.StatusInternalServerError)
		}
		return c.NoContent(http.StatusOK)
	})
	e.Logger.Fatal(e.Start(":80"))
}
