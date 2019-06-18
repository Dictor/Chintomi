<!--
<SQLite DB>
TABLE booklist((name TEXT, path TEXT)
-->
<?php
	require_once 'config/config.php';

	class mdl_book {
		private static $currentDB = NULL;
		private static $isconnected = FALSE;
		
		public static function InitSqlite() {
			if(!is_file(Config::sqlitePath)){
				$db = new SQLite3(Config::sqlitePath);
				$db->exec('CREATE TABLE booklist(name TEXT, path TEXT);');
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
				$res[]=new Comicbook($nowrow['name'], $nowrow['path']);
			}
			return $res;
		}
		
		public static function AddBook(Comicbook $book) {
			$currentDB->exec('INSERT INTO booklist (name, path) VALUES ("'.$book->name.'","'.$book->path.'");');
		}
		
		public static function DeleteBook(Comicbook $book) {
			DeleteBookByPath($book->path);
		}
		
		public static function DeleteBookByPath(string $path) {
			$currentDB->exec('DELETE FROM booklist WHERE name="'.$path.'";');
		}
	}

	class Comicbook {
		public $name, $path;
		
		public function __construct($name, $path) {
			$this->name = $name;
			$this->path = $path;
		}
	}
?>