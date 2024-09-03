package main

import (
	"fmt"
	"path/filepath"
	"testing"

	"github.com/spf13/afero"
	"github.com/stretchr/testify/assert"
	"github.com/stretchr/testify/suite"
)

type FileTestSuite struct {
	suite.Suite
	TargetFileSystem afero.Fs
}

func printTree(fs afero.Fs, path string, prefix string) error {
	fileInfo, err := fs.Stat(path)
	if err != nil {
		return err
	}

	fmt.Println(prefix + fileInfo.Name())

	if fileInfo.IsDir() {
		files, err := afero.ReadDir(fs, path)
		if err != nil {
			return err
		}

		// 디렉토리가 비어있는지 확인
		if len(files) == 0 {
			return nil
		}

		newPrefix := prefix + "├── "
		for i, file := range files {
			if i == len(files)-1 {
				newPrefix = prefix + "└── "
			}
			printTree(fs, filepath.Join(path, file.Name()), newPrefix)
		}
	}

	return nil
}

func (suite *FileTestSuite) SetupSuite() {
	// setting mock file system
	suite.TargetFileSystem = afero.NewMemMapFs()

	suite.TargetFileSystem.MkdirAll("/booka", 0755)       // valid book
	suite.TargetFileSystem.MkdirAll("/bookb/etc", 0755)   // valid book, but bookb/etc/* must be ignored
	suite.TargetFileSystem.MkdirAll("/group/bookc", 0755) // invalid book, no images on group/*, no recursive
	suite.TargetFileSystem.MkdirAll("/group/bookd", 0755) // invalid book, no images on group/*, no recursive
	suite.TargetFileSystem.MkdirAll("/booke", 0755)       // invalid book, no images on booke/*

	imageMaker := func(basepath string) {
		for i := 0; i < 10; i++ {
			assert.NoError(suite.T(), afero.WriteFile(suite.TargetFileSystem, fmt.Sprintf("%s/%d.jpg", basepath, i), []byte{}, 0755))
			assert.NoError(suite.T(), afero.WriteFile(suite.TargetFileSystem, fmt.Sprintf("%s/%d.exe", basepath, i), []byte{}, 0755))
		}
	}
	imageMaker("/booka")
	imageMaker("/bookb")
	imageMaker("/bookb/etc")
	imageMaker("/group/bookd")

	printTree(suite.TargetFileSystem, "/", "")
}

func (suite *FileTestSuite) TestImageFile() {
	_, err := ImageFile(suite.TargetFileSystem, "/bookb/0.jpg")
	assert.NoError(suite.T(), err)

	_, err = ImageFile(suite.TargetFileSystem, "invalid path")
	assert.ErrorIs(suite.T(), err, ErrFileNotFound)

	_, err = ImageFile(suite.TargetFileSystem, "/bookb/0.exe")
	assert.ErrorIs(suite.T(), err, ErrFileIsNotImage)

	//TODO: add test for file system error
}

func (suite *FileTestSuite) TestExploreBooks() {
	books, err := ExploreBooks(suite.TargetFileSystem, "/", ProviderCollection{})
	assert.NoError(suite.T(), err)
	assert.Len(suite.T(), books, 2)
	assert.Equal(suite.T(), books[0].ImageCount, 10)
	assert.Equal(suite.T(), books[1].ImageCount, 10)
}

func TestFileTestSuite(t *testing.T) {
	suite.Run(t, new(FileTestSuite))
}
