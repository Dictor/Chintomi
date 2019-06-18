<?php
	require_once 'config/config.php';
	require_once 'model/mdl_book.php';

	class library {
		public static function UpdateLibrary() {
			$validbooks = self::ExploreDirectory();
			$dbbooks = mdl_book::GetAllBooks();
			foreach(array_diff($validbooks, $dbbooks) as $nowadd) {
				mdl_book::AddBook(new Comicbook(NULL, $nowadd, array_pop(explode('\\', $nowadd)), ""));
			}
			foreach(array_diff($dbbooks, $validbooks) as $nowdel) {
				mdl_book::DeleteBookByPath($nowdel);
			}
		}
		
		private static function ExploreDirectory() {
			$res = array();
			$q = new \Ds\Queue();
			
			$q->push(Config::dataPath);
			while (!$q->isEmpty()) {
				$dircnt = $imgcnt = 0;
				foreach(GetEntry($nowpath = $q->pop()) as $nowentry) {
					if(is_dir($nowentry)) {
						$q->push($nowentry);
						$dircnt++;
					} elseif (is_file($nowentry) and isAllowedExt($nowentry)) {
						$imgcnt++;
					}
				}
				if ($dircnt == 0 and $imgcnt > 0) {
					$res[] = $nowpath;
				}
			}
			return $res;
		}
		
		private static function GetEntry(string $path) {
			$handle = opendir($path);
			$rtnval = array();

			while ($filename = readdir($handle)) {
				if($filename == '.' || $filename == '..') continue;
				$filepath = Config::dataPath.$filename;
				$rtnval[] = $filename;
			}
			closedir($handle);
			return $rtnval;
		}
		
		private static function isAllowedExt(string $path) {
			$ext = pathinfo($nowentry)['extension'];
			$res = FALSE;
			foreach(Config::allowedExt as $nowext) {
				if ($nowext == $ext) $res = TRUE;
			}
			return $res;
		}
	}
?>