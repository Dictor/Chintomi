<?php
    interface handler {
		public function Open($path);
		public function Close();
		public function Execute(string $preQuery, array $parameter);
		public function Query(string $preQuery, array $parameter);
		public function ResultToComicbook($res);
	}
	
	class hndSQLite implements handler{
		private static $currentDB;
		
		public function Open($path) {
			if(!is_file($path)){
				$db = new SQLite3($path);
				$db->exec('CREATE TABLE comicbook(book_id INTEGER PRIMARY KEY AUTOINCREMENT, book_path TEXT NOT NULL, book_name TEXT, book_author TEXT);');
				$db->close();
			}
			
			$db = new SQLite3($path);
			self::$currentDB = $db;
			return $db->lastErrorCode();
		}
		
		public function Close() {
			self::$currentDB->close();
		}
		
		public function Execute(string $preQuery, array $parameter) {
			$state = self::$currentDB->prepare($preQuery);
			$state->execute($parameter);
		}
		
		public function Query(string $preQuery, array $parameter) {
			$state = self::$currentDB->prepare($preQuery);
			return $state->query($parameter);
		}
		
		public function ResultToComicbook($res) {
			$arr = array();
			while ($nowrow = $res->fetchArray()) {
				$arr[]=new Comicbook($nowrow['book_id'], $nowrow['book_path'], $nowrow['book_name'], $nowrow['book_author']);
			}
			return $arr;
		}
	}
?>