package main

import (
	"path/filepath"
	"reflect"

	"github.com/samber/lo"
	"github.com/spf13/afero"
)

const (
	ProviderMatchPerfect    ProviderMatchResult = iota // Use returning provider firstly
	ProviderMatchPossible                              // Use returning provider as fallback
	ProviderMatchImpossible                            // Don't use returning provider
)

type (
	// Provider is comic book information provider from file system
	Provider interface {
		ProviderString() string
		FillBook(*Book) error
		TestBookMatch(*Book) ProviderMatchResult
	}

	ProviderCollection  []Provider
	ProviderMatchResult int

	RawProvider struct {
		fs *afero.Fs
	}
)

func (pc *ProviderCollection) Register(p Provider) {
	exist := lo.ContainsBy[Provider](*pc, func(item Provider) bool {
		return reflect.TypeOf(item) == reflect.TypeOf(p)
	})

	if !exist {
		*pc = append(*pc, p)
	}
}

func (pc ProviderCollection) MatchProvider(book *Book) (Provider, bool) {
	var fallback Provider
	providerFound := false

	for _, p := range pc {
		result := p.TestBookMatch(book)

		switch result {
		case ProviderMatchPerfect:
			return p, true
		case ProviderMatchPossible:
			fallback = p
			providerFound = true
		}
	}

	if providerFound {
		return fallback, true
	} else {
		return nil, false
	}
}

func NewRawProvider(fs *afero.Fs) RawProvider {
	return RawProvider{
		fs: fs,
	}
}

func (RawProvider) ProviderString() string {
	return "raw"
}

func (RawProvider) TestBookMatch(*Book) ProviderMatchResult {
	return ProviderMatchPossible
}

/*
	ID            string
	Name          string
	Author        string
	Tag           []string
	HasProvider   bool
	Provider      Provider
	HasThumbnail  bool
	ThumbnailFile string
*/

func (p RawProvider) FillBook(book *Book) error {
	book.ID = filepath.Base(book.Path)
	book.Name = book.ID
	book.Author = ""
	book.Tag = []string{}
	book.HasProvider = true
	book.Provider = p
	book.HasThumbnail = true
	book.ThumbnailFile = book.ImageFiles[0]

	return nil
}
