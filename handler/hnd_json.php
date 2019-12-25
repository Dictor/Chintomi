<?php
namespace Dictor\Chintomi;
require_once 'vendor/autoload.php';

use Jajo\JSONDB;

class hnd_json implements handler {
	const TABLE_USER = 'user.json';
	const TABLE_BOOK = 'book.json';
	private static $current_db;
	
	/* 
		Unlike sqlite handler, json handler use one folder as database (in sql) and one json file as table (in sql).
		So, this function takes path in directory. 
	*/
	public static function Open(string $path): int { 
		if(!is_dir($path)) mkdir($path, 0777, TRUE);
        self::$current_db = new JSONDB((\mb_substr($path, -1) != '/' ? $path : $path.'/'));
        if (is_dir($path)) {
        	return 0;
        } else {
        	return -1;
        }
	}
	
	public static function Close(): void {
		unset(self::$current_db);
	}
	
	public static function GetDB(): \Jajo\JSONDB {
		return self::$current_db;
	}
	
	public static function ResultToArray($res): array {
		$rtn = [];
		foreach ($res as $nowmem) {
			if (gettype($nowmem) == 'object') {
				$rtn[] = get_object_vars($nowmem);
			} else if (gettype($nowmem) == 'array') {
				$rtn[] = $nowmem;
			}
		}
		return $rtn;
	}
	
	public static function ResultToComicbook($res): array {
		$rtn = [];
    	$res = self::ResultToArray($res);
    	foreach ($res as $nowrow) {
    		$rtn[]=new Comicbook($nowrow['book_id'], $nowrow['book_path'], $nowrow['book_name'], $nowrow['book_author'], $nowrow['image_count'], $nowrow['image_size'], $nowrow['added_date']);
    	}
    	return $rtn;
	}
}
?>