package main

import (
	"fmt"
	"os"
	"testing"

	"github.com/spf13/afero"
	"github.com/stretchr/testify/assert"
)

func TestExploreBooks(t *testing.T) {
	// setting mock file system
	fs := afero.NewMemMapFs()

	fs.MkdirAll("/booka", 0755)       // valid book
	fs.MkdirAll("/bookb/etc", 0755)   // valid book, but bookb/etc/* must be ignored
	fs.MkdirAll("/group/bookc", 0755) // invalid book, no images on group/*, no recursive
	fs.MkdirAll("/group/bookd", 0755) // invalid book, no images on group/*, no recursive
	fs.MkdirAll("/booke", 0755)       // invalid book, no images on booke/*

	imageMaker := func(basepath string) {
		for i := 0; i < 10; i++ {
			assert.NoError(t, afero.WriteFile(fs, fmt.Sprintf("%s/%d.jpg", basepath, i), []byte{}, 0755))
			assert.NoError(t, afero.WriteFile(fs, fmt.Sprintf("%s/%d.exe", basepath, i), []byte{}, 0755))
		}
	}
	imageMaker("/booka")
	imageMaker("/bookb")
	imageMaker("/bookb/etc")
	imageMaker("/group/bookd")

	fmt.Println("@@@@ Test directory structure")
	afero.Walk(fs, "/", func(path string, info os.FileInfo, err error) error {
		if info.IsDir() {
			fmt.Printf("\n%s\n", path)
		} else {
			fmt.Printf("%s ", path)
		}

		return nil
	})
	fmt.Println("@@@@@@@@@@@@@@@@@@@@@@@@@@@@")

	// test
	books, err := ExploreBooks(fs, "/")
	assert.NoError(t, err)
	assert.Len(t, books, 2)
	assert.Equal(t, books[0].ImageCount, 10)
	assert.Equal(t, books[1].ImageCount, 10)
}
