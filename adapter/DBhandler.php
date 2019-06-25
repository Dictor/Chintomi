<?php
    interface handler {
		public static function Open($path);
		public static function Close();
		public static function Execute(string $preQuery, array $parameter);
		public static function Query(string $preQuery, array $parameter);
		public static function ResultToComicbook($res);
	}
	
	class hndSQLite implements handler{
		private static $currentDB;
		private static $isOpen = FALSE;
		
		public static function Open($path) {
			if(!is_file($path)){
				$db = new SQLite3($path);
				$db->exec('CREATE TABLE comicbook(book_id INTEGER PRIMARY KEY AUTOINCREMENT, book_path TEXT NOT NULL, book_name TEXT, book_author TEXT);');
				$db->close();
			}
			
			if(!self::$isOpen){
				$db = new SQLite3($path);
				self::$currentDB = $db;
				if ($db->lastErrorCode()) self::$isOpen = TRUE;
				return $db->lastErrorCode();	
			}
		}
		
		public static function Close() {
			self::$currentDB->close();
		}
		
		public static function Execute(string $preQuery, array $parameter) {
			$state = self::$currentDB->prepare($preQuery);
			$state->execute($parameter);
		}
		
		public static function Query(string $preQuery, array $parameter) {
			$state = self::$currentDB->prepare($preQuery);
			return $state->query($parameter);
		}
		
		public static function ResultToComicbook($res) {
			$arr = array();
			while ($nowrow = $res->fetchArray()) {
				$arr[]=new Comicbook($nowrow['book_id'], $nowrow['book_path'], $nowrow['book_name'], $nowrow['book_author']);
			}
			return $arr;
		}
	}
?>