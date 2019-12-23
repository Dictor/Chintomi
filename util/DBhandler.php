<?php
	namespace Dictor\Chintomi;
	
    interface handler {
		public static function Open($path): int;
		public static function Close(): void;
		public static function Execute(string $preQuery, array $parameter): \SQLite3Result;
		public static function Query(string $preQuery, array $parameter): \SQLite3Result;
		public static function ResultToArray($res): array;
		public static function ResultToComicbook($res): array;
	}
	
	class hnd_SQLite implements handler{
		private static $currentDB;
		private static $isOpen = FALSE;
		
		public static function Open($path): int {
			try{
				if(!is_dir(dirname($path))) mkdir(dirname($path), 0777, TRUE);
				if(!is_file($path)){
					$db = new \SQLite3($path);
					$db->exec('CREATE TABLE comicbook(book_id INTEGER PRIMARY KEY AUTOINCREMENT, book_path TEXT NOT NULL, book_name TEXT, book_author TEXT, image_count INTEGER, image_size INTEGER, added_date TEXT);');
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
			} catch(\Throwable $t) {
				return -1;
			}
		}
		
		public static function Close(): void {
			self::$currentDB->close();
		}
		
		public static function Execute(string $preQuery, array $parameter): \SQLite3Result {
			return self::Query($preQuery, $parameter);
		}
		
		public static function Query(string $preQuery, array $parameter): \SQLite3Result {
			$state = self::$currentDB->prepare($preQuery);
			if($state == FALSE) return FALSE;
			foreach($parameter as $nowkey => $nowval) {
				$state->bindValue($nowkey, $nowval);
			}
			return $state->execute();
		}
		
		public static function ResultToArray($res): array {
			$arr = array();
			while ($nowrow = $res->fetchArray(SQLITE3_ASSOC)){
				if($nowrow != FALSE) $arr[] = $nowrow;
			}
			return $arr;
		}
		
		public static function ResultToComicbook($res): array {
			$arr = array();
			while ($nowrow = $res->fetchArray()) {
				$arr[]=new Comicbook($nowrow['book_id'], $nowrow['book_path'], $nowrow['book_name'], $nowrow['book_author'], $nowrow['image_count'], $nowrow['image_size'], $nowrow['added_date']);
			}
			return $arr;
		}
	}
?>