package main

import (
	"fmt"
	"testing"
	"time"

	"github.com/spf13/afero"
	"github.com/stretchr/testify/assert"
)

func TestRawProvider(t *testing.T) {
	fs := afero.NewMemMapFs()
	rp := NewRawProvider(&fs)
	assert.Equal(t, rp.ProviderString(), "raw")

	b := Book{
		Path:          "/parent/booka",
		ImageCount:    5,
		ImageSize:     100000,
		AddedDate:     time.Now(),
		NonImageFiles: []string{""},
		ImageFiles:    []string{""},
	}
	rp.FillBook(&b)
	fmt.Printf("%#v", b)

	assert.Equal(t, b.Name, "booka")
}
