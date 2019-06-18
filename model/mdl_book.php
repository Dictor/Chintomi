<!--
<SQLite DB>
TABLE comicbook(book_id INTEGER PRIMARY KEY AUTOINCREMENT, book_path TEXT NOT NULL, book_name TEXT, book_author TEXT)  
-->
<?php
	require_once 'config/config.php';

	class mdl_book {
		private static $currentDB = NULL;
		private static $isconnected = FALSE;
		
		public static function InitSqlite() {
			if(!is_file(Config::sqlitePath)){
				$db = new SQLite3(Config::sqlitePath);
				$db->exec('book_id INTEGER PRIMARY KEY AUTOINCREMENT, book_path TEXT NOT NULL, book_name TEXT, book_author TEXT);');
				$db->close();
			}
			
			$db = new SQLite3(Config::sqlitePath);
			if($db->lastErrorCode() != 0){
				return true;
				$currentDB = $db;
				$isconnected = TRUE;
			} else {
				return false;
				$currentDB = NULL;
				$isconnected = FALSE;
			}
		}
		
		public static function CloseSqlite() {
			$currentDB->close();
			$isconnected = FALSE();
		}
		
		public static function GetAllBooks() {
			$qres = $currentDB->query('SELECT * FROM booklist');
			$res = array();
			while ($nowrow = $qres->fetch()) {
				$res[]=new Comicbook($nowrow['book_id'], $nowrow['book_path'], $nowrow['book_name'], $nowrow['book_author']);
			}
			return $res;
		}
		
		public static function AddBook(Comicbook $book) {
			$currentDB->exec('INSERT INTO booklist (book_path, book_name, book_author) VALUES ("'.$book->path.'","'.$book->name.'","'.$book->author.'");');
		}
		
		public static function DeleteBook(Comicbook $book) {
			$currentDB->exec('DELETE FROM booklist WHERE book_id="'.$book->$id.'";');
		}
		
		public static function DeleteBookByPath(string $path) {
			$currentDB->exec('DELETE FROM booklist WHERE name="'.$path.'";');
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