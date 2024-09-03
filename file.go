package main

import (
	"errors"
	"path/filepath"
	"strings"

	"github.com/samber/lo"
	"github.com/spf13/afero"
)

type (
	FileSystemError struct {
		AferoError error
	}
)

var (
	ErrFileNotFound   = errors.New("file not found")
	ErrFileIsNotImage = errors.New("file didn't have image extension")

	allowedExtensionList []string = []string{".jpg", ".jpeg", ".webp", ".png", ".gif"}
)

// ImageFile reads the file at the given path from the provided file system and returns its binary data.
// It also performs checks to ensure the file exists and has an allowed image extension.
//
// Possible errors returned:
//   - FileSystemError: An error occurred while interacting with the file system (e.g., checking file existence, reading file, getting file info).
//   - ErrFileNotFound: The file specified by the path does not exist.
//   - ErrFileIsNotImage: The file does not have an allowed image extension.
func ImageFile(fs afero.Fs, path string) ([]byte, error) {
	if exist, err := afero.Exists(fs, path); err != nil {
		return nil, &FileSystemError{err}
	} else if !exist {
		return nil, ErrFileNotFound
	}

	info, err := fs.Stat(path)
	if err != nil {
		return nil, &FileSystemError{err}
	}
	if !AllowedExtension(info.Name()) {
		return nil, ErrFileIsNotImage
	}

	bin, err := afero.ReadFile(fs, path)
	if err != nil {
		return nil, &FileSystemError{err}
	} else {
		return bin, nil
	}
}

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
func ExploreBooks(fs afero.Fs, path string, pc ProviderCollection) ([]Book, error) {
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
			nonImagePathList := []string{}
			imageSize := int64(0)
			for _, c := range childList {
				childPath := parentPath + "/" + c.Name()
				if AllowedExtension(c.Name()) && !c.IsDir() {
					imagePathList = append(imagePathList, childPath)
					imageSize += c.Size()
				} else {
					nonImagePathList = append(nonImagePathList, childPath)
				}
			}

			if len(imagePathList) > 0 {
				b := Book{
					Path:          parentPath,
					ImageCount:    len(imagePathList),
					ImageSize:     int(imageSize),
					AddedDate:     p.ModTime(),
					ImageFiles:    imagePathList,
					NonImageFiles: nonImagePathList,
				}

				if p, pSuccess := pc.MatchProvider(&b); pSuccess {
					p.FillBook(&b)
				} else {
					b.HasProvider = false
				}

				ret = append(ret, b)
			}
		}
	}

	return ret, nil
}

func AllowedExtension(name string) bool {
	ext := strings.ToLower(filepath.Ext(name))
	return lo.Contains(allowedExtensionList, ext)
}

func (err *FileSystemError) Error() string {
	return err.AferoError.Error()
}
