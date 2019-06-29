<?php
	require_once 'config/config.php';
	require_once 'util/DBhandler.php';

	class Comicbook {
		public $name, $path, $id, $author;
		public function __construct($id, $path, $name, $author) {
			$this->name = $name;
			$this->path = $path;
			$this->id = $id;
			$this->author = $author;
		}
	}

	class mdl_book {
		public static function UseDB() {
			hndSQLite::Open(config::PATH_SQLITE);
		}
		
		public static function GetAllBooks() {
			return hndSQLite::ResultToComicbook(hndSQLite::Query('SELECT * FROM comicbook', array()));
		}
		
		public static function AddBook(Comicbook $book) {
			return hndSQLite::Execute('INSERT INTO comicbook (book_path, book_name, book_author) VALUES (:bpath, :bname, :bauthor)', array('bpath' => $book->path, 'bname' => $book->name, 'bauthor' => $book->author));
		}
		
		public static function DeleteBook(Comicbook $book) {
			return hndSQLite::Execute('DELETE FROM comicbook WHERE book_id=:bid', array('bid' => $book->$id));
		}
		
		public static function DeleteBookByPath(string $path) {
			return hndSQLite::Execute('DELETE FROM comicbook WHERE book_path=:bpath', array('path' => $path));
		}
		
		public static function SearchBook(string $id) {
			return hndSQLite::ResultToComicbook(hndSQLite::Query('SELECT * FROM comicbook WHERE book_id=:bid', array('bid' => $id)));
		}
	}
?>