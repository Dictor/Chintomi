package main

import (
	"path/filepath"
	"strings"

	"github.com/samber/lo"
	"github.com/spf13/afero"
)

var allowedExtensionList []string = []string{".jpg", ".jpeg", ".webp", ".png", ".gif"}

// ExploreBooks explores the given directory path within the provided afero.Fs filesystem and parses its immediate subdirectories as books.
// It does not recursively explore deeper levels of the directory structure.
//
// For example:
//   - path/booka/*.jpg will be parsed as a single book named "booka".
//   - path/booka/etc/*.jpg will not be parsed as they are in a subdirectory of "booka".
//   - If both path/kind/booka and path/kind/bookb exist, neither will be parsed, only files directly in the "kind" directory will be considered.
//
// Parameters:
//   - fs: the afero.Fs filesystem to explore.
//   - path: the directory path within the filesystem to explore for books.
//
// Returns:
//   - a slice of Book structures representing the parsed books.
func ExploreBooks(fs afero.Fs, path string) ([]Book, error) {
	ret := []Book{}
	bpfs := afero.NewBasePathFs(fs, path)

	parentList, err := afero.ReadDir(bpfs, "/")
	if err != nil {
		return ret, err
	}

	for _, p := range parentList {
		if p.IsDir() {
			parentPath := "/" + p.Name()
			childList, err := afero.ReadDir(bpfs, parentPath)
			if err != nil {
				continue
			}

			imagePathList := []string{}
			imageSize := int64(0)
			for _, c := range childList {
				if AllowedExtension(c.Name()) && !c.IsDir() {
					childPath := parentPath + "/" + c.Name()
					imagePathList = append(imagePathList, childPath)
					imageSize += c.Size()
				}
			}

			if len(imagePathList) > 0 {
				ret = append(ret, Book{
					ID:         parentPath,
					Name:       p.Name(),
					Path:       parentPath,
					Author:     "",
					ImageCount: len(imagePathList),
					ImageSize:  int(imageSize),
					AddedDate:  p.ModTime(),
					ImageFiles: imagePathList,
				})
			}
		}
	}

	return ret, nil
}

func AllowedExtension(name string) bool {
	ext := strings.ToLower(filepath.Ext(name))
	return lo.Contains(allowedExtensionList, ext)
}
