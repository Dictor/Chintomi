<?php
	namespace Dictor\Chintomi;
	
	require_once 'config/config.php';
	require_once 'model/mdl_book.php';
	require_once 'controller/ctr_viewer.php';
	require_once 'vendor/autoload.php';
	use \Ds\Queue;
	use \Gumlet\ImageResize;

	class library {
		public static $thumbdir = Config::PATH_COMICBOOK.'/.thumbnail';
		
		public static function UpdateLibrary() {
			$validpaths = self::ExploreDirectory();
			$dbbooks = mdl_book::GetAllBooks();
			$dbpaths = array();
			foreach($dbbooks as $nowbook) {
				$dbpaths[] = $nowbook->path;
			}
			foreach(array_diff($validpaths, $dbpaths) as $nowadd) {
				$name = explode('/',$nowadd);
				mdl_book::AddBook(new Comicbook(NULL, $nowadd, end($name), ""));
			}
			foreach(array_diff($dbpaths, $validpaths) as $nowdel) {
				mdl_book::DeleteBookByPath($nowdel);
			}
		}
		
		public static function UpdateThumbnail() {
			if (!is_dir(self::$thumbdir)) mkdir(self::$thumbdir);
			$dbbooks = mdl_book::GetAllBooks();
			foreach($dbbooks as $nowbook) {
				$thumbsrc = ctr_viewer::GetImagePath(library::GetEntry($nowbook->path), 1);
				if(!is_file(self::$thumbdir.'/'.(string)$nowbook->id.'.jpg')) {
					$image = new ImageResize($thumbsrc);
					$image->quality_jpg = Config::THUMBNAIL_QUALITY;
					$image->resizeToLongSide(Config::THUMBNAIL_LONGSIDE_LENGTH);
					$image->save(self::$thumbdir.'/'.(string)$nowbook->id.'.jpg', IMAGETYPE_JPEG);
				}
			}
		}
		
		private static function ExploreDirectory() {
			$res = array();
			$q = new Queue();
			
			$q->push(Config::PATH_COMICBOOK);
			while (!$q->isEmpty()) {
				$dircnt = $imgcnt = 0;
				foreach(self::GetEntry($nowpath = $q->pop()) as $nowentry) {
					if(is_dir($nowentry)) {
						$q->push($nowentry);
						$dircnt++;
					} elseif (is_file($nowentry) and self::isAllowedExt($nowentry)) {
						$imgcnt++;
					}
				}
				if ($dircnt == 0 and $imgcnt > 0) {
					$res[] = $nowpath;
				}
			}
			return $res;
		}
		
		public static function GetEntry(string $path) {
			$handle = opendir($path);
			$rtnval = array();

			while ($entryname = readdir($handle)) {
				if(mb_substr($entryname, 0, 1) == '.') continue;
				$entryname = $path.'/'.$entryname;
				if(is_dir($entryname) or self::isAllowedExt($entryname)) $rtnval[] = $entryname;
			}
			closedir($handle);
			return $rtnval;
		}
		
		private static function isAllowedExt(string $path) {
			$ext = pathinfo($path)['extension'];
			$res = FALSE;
			foreach(Config::ALLOWED_EXTENSION as $nowext) {
				if ($nowext == $ext) $res = TRUE;
			}
			return $res;
		}
	}
?>