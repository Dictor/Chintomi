package main

import (
	"time"
)

type (
	//Book is definition of each comic book.
	Book struct {
		ID         string
		Name       string
		Path       string
		Author     string
		ImageCount int
		ImageSize  int
		AddedDate  time.Time
	}

	//User is definition of each login user.
	User struct {
		Name       string
		Password   string
		Permission int
	}
)
