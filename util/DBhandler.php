<?php
	namespace Dictor\Chintomi;
	
    interface handler {
		public static function Open($path);
		public static function Close();
		public static function Execute(string $preQuery, array $parameter);
		public static function Query(string $preQuery, array $parameter);
		public static function ResultToArray($res);
		public static function ResultToComicbook($res);
	}
	
	class hndSQLite implements handler{
		private static $currentDB;
		private static $isOpen = FALSE;
		
		public static function Open($path) {
			if(!is_file($path)){
				$db = new \SQLite3($path);
				$db->exec('CREATE TABLE comicbook(book_id INTEGER PRIMARY KEY AUTOINCREMENT, book_path TEXT NOT NULL, book_name TEXT, book_author TEXT);');
				$db->exec('CREATE TABLE user(user_name TEXT PRIMARY KEY NOT NULL, user_pass TEXT NOT NULL, user_permission INTEGER NOT NULL);');
				$db->close();
			}
			
			if(!self::$isOpen){
				$db = new \SQLite3($path);
				self::$currentDB = $db;
				if ($db->lastErrorCode() == 0) self::$isOpen = TRUE;
				return $db->lastErrorCode();	
			} else {
				return 0;
			}
		}
		
		public static function Close() {
			self::$currentDB->close();
		}
		
		public static function Execute(string $preQuery, array $parameter) {
			return self::Query($preQuery, $parameter);
		}
		
		public static function Query(string $preQuery, array $parameter) {
			$state = self::$currentDB->prepare($preQuery);
			if($state == FALSE) return FALSE;
			foreach($parameter as $nowkey => $nowval) {
				$state->bindValue($nowkey, $nowval);
			}
			return $state->execute();
		}
		
		public static function ResultToArray($res) {
			$arr = array();
			while ($nowrow = $res->fetchArray(SQLITE3_ASSOC)){
				if($nowrow != FALSE) $arr[] = $nowrow;
			}
			return $arr;
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