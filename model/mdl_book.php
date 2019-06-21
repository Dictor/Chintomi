<?php
	/*
	<SQLite DB>
	TABLE comicbook(book_id INTEGER PRIMARY KEY AUTOINCREMENT, book_path TEXT NOT NULL, book_name TEXT, book_author TEXT)  
	*/
	require_once 'config/config.php';

	class mdl_book {
		private static $currentDB = NULL;
		
		public static function InitSqlite() {
			if(!is_file(Config::sqlitePath)){
				$db = new SQLite3(Config::sqlitePath);
				$db->exec('CREATE TABLE comicbook(book_id INTEGER PRIMARY KEY AUTOINCREMENT, book_path TEXT NOT NULL, book_name TEXT, book_author TEXT);');
				$db->close();
			}
			
			$db = new SQLite3(Config::sqlitePath);
			self::$currentDB = $db;
			if($db->lastErrorCode() != 0){
				echo "DB Error : ".$db->lastErrorCode();
				return FALSE;
			} else {
				return TRUE;
			}
		}
		
		public static function CloseSqlite() {
			self::$currentDB->close();
		}
		
		public static function GetAllBooks() {
			return self::QueryResultToArray(self::$currentDB->query('SELECT * FROM comicbook'));
		}
		
		public static function AddBook(Comicbook $book) {
			self::$currentDB->exec('INSERT INTO comicbook (book_path, book_name, book_author) VALUES ("'.$book->path.'","'.$book->name.'","'.$book->author.'");');
		}
		
		public static function DeleteBook(Comicbook $book) {
			self::$currentDB->exec('DELETE FROM comicbook WHERE book_id="'.$book->$id.'";');
		}
		
		public static function DeleteBookByPath(string $path) {
			self::$currentDB->exec('DELETE FROM comicbook WHERE name="'.$path.'";');
		}
		
		public static function SearchBook(string $id) {
			return self::QueryResultToArray(self::$currentDB->query('SELECT * FROM comicbook WHERE book_id='.$id));
		}
		
		private static function QueryResultToArray($qres) {
			$res = array();
			while ($nowrow = $qres->fetchArray()) {
				$res[]=new Comicbook($nowrow['book_id'], $nowrow['book_path'], $nowrow['book_name'], $nowrow['book_author']);
			}
			return $res;
		}
	}

	class Comicbook {
		public $name, $path, $id, $author;
		public function __construct($id, $path, $name, $author) {
			$this->name = $name;
			$this->path = $path;
			$this->id = $id;
			$this->author = $author;
		}
	}
?>