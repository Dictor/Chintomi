<?php
	namespace Dictor\Chintomi;
	require_once 'autoload.php';

	class Comicbook {
		public $name, $path, $id, $author, $imgcnt, $imgsize, $added_date;
		public function __construct($id, $path, $name, $author, $imgcnt, $imgsize, $added_date) {
			$this->name = $name;
			$this->path = $path;
			$this->id = $id;
			$this->author = $author;
			$this->imgcnt = $imgcnt;
			$this->imgsize = $imgsize;
			$this->added_date = $added_date;
		}
	}

	class mdl_book {
		public static function UseDB(): int {
			switch (config::DB_HANDLER) {
                case 'SQLITE': return hnd_SQLite::Open(config::PATH_SQLITE);
			}
		}
		
		public static function GetAllBooks(): array {
			switch (config::DB_HANDLER) {
                case 'SQLITE': return hnd_SQLite::ResultToComicbook(hnd_SQLite::Query('SELECT * FROM comicbook', array()));
			}
		}
		
		public static function AddBook(Comicbook $book): void {
			switch (config::DB_HANDLER) {
                case 'SQLITE':
					hnd_SQLite::Execute('INSERT INTO comicbook (book_path, book_name, book_author, image_count, image_size, added_date) VALUES (:bpath, :bname, :bauthor, :bimgcnt, :bimgsize, :bdate)', 
						array('bpath' => $book->path, 'bname' => $book->name, 'bauthor' => $book->author, 'bimgcnt' => $book->imgcnt, 'bimgsize' => $book->imgsize, 'bdate' => (new \DateTime('now')) -> format(\DateTime::ATOM)));
					break;
			}
		}
		
		public static function DeleteBook(Comicbook $book): void {
			switch (config::DB_HANDLER) {
                case 'SQLITE': hnd_SQLite::Execute('DELETE FROM comicbook WHERE book_id=:bid', array('bid' => $book->$id)); break;
			}
		}
		
		public static function DeleteAllBooks(): void {
			switch (config::DB_HANDLER) {
                case 'SQLITE': hnd_SQLite::Execute('DELETE FROM comicbook', array()); break;
			}
		}
		
		public static function DeleteBookByPath(string $path): void {
			switch (config::DB_HANDLER) {
                case 'SQLITE': hnd_SQLite::Execute('DELETE FROM comicbook WHERE book_path=:bpath', array('bpath' => $path)); break;
			}
		}
		
		public static function SearchBook(string $id): array {
			switch (config::DB_HANDLER) {
                case 'SQLITE': return hnd_SQLite::ResultToComicbook(hnd_SQLite::Query('SELECT * FROM comicbook WHERE book_id=:bid', array('bid' => $id)));
			}
		}
		
		public static function GetHumanFileSize(int $bytes, int $decimals = 2): string {
		  $sz = 'BKMGTP';
		  $factor = floor((strlen($bytes) - 1) / 3);
		  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
		}
	}
?>