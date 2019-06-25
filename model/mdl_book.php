<?php
	/*
	<SQLite DB>
	TABLE comicbook(book_id INTEGER PRIMARY KEY AUTOINCREMENT, book_path TEXT NOT NULL, book_name TEXT, book_author TEXT)  
	*/
	require_once 'config/config.php';
	require_once 'adapter/DBhandler.php';

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
		public static function GetAllBooks() {
			return hndSQLite::QueryResultToComicbook(hndSQLite::Query('SELECT * FROM comicbook', array()));
		}
		
		public static function AddBook(Comicbook $book) {
			return hndSQLite::Execute('INSERT INTO comicbook (book_path, book_name, book_author) VALUES (?, ?, ?)', array($book->path, $book->name, $book->author));
		}
		
		public static function DeleteBook(Comicbook $book) {
			return hndSQLite::Execute('DELETE FROM comicbook WHERE book_id=?', array($book->$id));
		}
		
		public static function DeleteBookByPath(string $path) {
			return hndSQLite::Execute('DELETE FROM comicbook WHERE name=?', array($path));
		}
		
		public static function SearchBook(string $id) {
			return hndSQLite::QueryResultToComicbook(hndSQLite::Query('SELECT * FROM comicbook WHERE book_id=?', array($id)));
		}
	}
?>