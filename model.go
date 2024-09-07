package main

import (
	"time"
)

type (
	BookId string

	//Book is definition of each comic book
	Book struct {
		// Filled by ExploreBook
		Path          string
		ImageCount    int
		ImageSize     int
		AddedDate     time.Time
		NonImageFiles []string
		ImageFiles    []string

		// Filled by Provider
		ID            BookId
		Name          string
		Author        string
		Tag           []string
		HasProvider   bool
		Provider      Provider
		HasThumbnail  bool
		ThumbnailFile string
	}

	//User is definition of each login user
	User struct {
		Name       string
		Password   string
		Permission int
	}
)
