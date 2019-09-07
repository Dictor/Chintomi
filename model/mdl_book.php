<?php
	namespace Dictor\Chintomi;
	require_once 'autoload.php';

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
            return hnd_SQLite::Open(config::PATH_SQLITE);
		}
		
		public static function GetAllBooks() {
			return hnd_SQLite::ResultToComicbook(hnd_SQLite::Query('SELECT * FROM comicbook', array()));
		}
		
		public static function AddBook(Comicbook $book) {
			return hnd_SQLite::Execute('INSERT INTO comicbook (book_path, book_name, book_author) VALUES (:bpath, :bname, :bauthor)', array('bpath' => $book->path, 'bname' => $book->name, 'bauthor' => $book->author));
		}
		
		public static function DeleteBook(Comicbook $book) {
			return hnd_SQLite::Execute('DELETE FROM comicbook WHERE book_id=:bid', array('bid' => $book->$id));
		}
		
		public static function DeleteBookByPath(string $path) {
			return hnd_SQLite::Execute('DELETE FROM comicbook WHERE book_path=:bpath', array('bpath' => $path));
		}
		
		public static function SearchBook(string $id) {
			return hnd_SQLite::ResultToComicbook(hnd_SQLite::Query('SELECT * FROM comicbook WHERE book_id=:bid', array('bid' => $id)));
		}
	}
?>