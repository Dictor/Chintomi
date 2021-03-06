<?php
	namespace Dictor\Chintomi;
	require_once 'autoload.php';
	require_once 'vendor/autoload.php';
	use \Ds\Queue;
	use \Gumlet\ImageResize;
	use \Symfony\Component\Finder\Finder;

	class mdl_library {
		public static $thumbdir = config::PATH_COMICBOOK.'/.thumbnail';
		
		public static function UpdateLibrary(): array {
			$validinfo = self::NewExploreDirectory();
			$validpaths = array();
			foreach($validinfo as $nowinfo) {
				$validpaths[] = $nowinfo['path'];
			}
			
			$dbbooks = mdl_book::GetAllBooks();
			$dbpaths = array();
			$res = array('added' => 0, 'deleted' => 0);
			
			foreach($dbbooks as $nowbook) {
				$dbpaths[] = $nowbook->path;
			}
			foreach(array_diff($validpaths, $dbpaths) as $nowadd) {
				$name = explode('/',$nowadd);
				$info;
				foreach ($validinfo as $nowinfo) {
					if ($nowinfo['path'] === $nowadd) {
						$info = $nowinfo;
						break;
					}
				}
				mdl_book::AddBook(new Comicbook(NULL, $nowadd, end($name), "", $info['imgcnt'], $info['imgsize'], NULL));
				$res['added']++;
			}
			foreach(array_diff($dbpaths, $validpaths) as $nowdel) {
				mdl_book::DeleteBookByPath($nowdel);
				$res['deleted']++;
			}
			return $res;
		}
		
		public static function ResetLibrary(): void {
			mdl_book::DeleteAllBooks();
		}
		
		public static function UpdateThumbnail(): int {
			if (config::MEMORY_UNLIMIT_UPDATE_THUMBNAIL) ini_set('memory_limit', '-1');
			if (!is_dir(self::$thumbdir)) mkdir(self::$thumbdir);
			$dbbooks = mdl_book::GetAllBooks();
			$res = 0;
			foreach($dbbooks as $nowbook) {
				$thumbsrc = ctr_viewer::GetImagePath(mdl_library::GetEntry($nowbook->path), 1);
				if(!is_file(self::$thumbdir.'/'.(string)$nowbook->id.'.jpg')) {
					$image = new ImageResize($thumbsrc);
					$image->quality_jpg = config::THUMBNAIL_QUALITY;
					$image->resizeToLongSide(config::THUMBNAIL_LONGSIDE_LENGTH);
					$image->save(self::$thumbdir.'/'.(string)$nowbook->id.'.jpg', IMAGETYPE_JPEG);
					$res++;
					unset($image);
				}
			}
			return $res;
		}
		
		public static function UpdateThumbnailNext(): int {
			if (config::MEMORY_UNLIMIT_UPDATE_THUMBNAIL) ini_set('memory_limit', '-1');
			if (!is_dir(self::$thumbdir)) mkdir(self::$thumbdir);
			$dbbooks = mdl_book::GetAllBooks();
			$res = -1;
			foreach($dbbooks as $nowbook) {
				$thumbsrc = ctr_viewer::GetImagePath(mdl_library::GetEntry($nowbook->path), 1);
				if(!is_file(self::$thumbdir.'/'.(string)$nowbook->id.'.jpg')) {
					$image = new ImageResize($thumbsrc);
					$image->quality_jpg = config::THUMBNAIL_QUALITY;
					$image->resizeToLongSide(config::THUMBNAIL_LONGSIDE_LENGTH);
					$image->save(self::$thumbdir.'/'.(string)$nowbook->id.'.jpg', IMAGETYPE_JPEG);
					$res++;
					unset($image);
					$res = (string)$nowbook->id;
					break;
				}
			}
			return $res;
		}

		public static function NewExploreDirectory(): array {
			$finder = new Finder();
			$finder->files()->in(config::PATH_COMICBOOK);
			$dirres = array();

			foreach ($finder as $file) {
				$path = $file->getRealPath();
				if(!self::isAllowedExt($path)) continue;

				$dir = dirname($path);
				if(!array_key_exists($dir, $dirres)){
					$dirres[$dir] = array('path' => $dir, 'imgcnt' => 0, 'imgsize' => 0);
				}
				$dirres[$dir]['imgcnt']++;
				$dirres[$dir]['imgsize'] += filesize($path);
			}

			return array_values($dirres);
		}
		
		private static function ExploreDirectory(): array {
			$res = array();
			$q = new Queue();
			
			$q->push(config::PATH_COMICBOOK);
			while (!$q->isEmpty()) {
				$dircnt = $imgcnt = $imgsize = 0;
				foreach(self::GetEntry($nowpath = $q->pop()) as $nowentry) {
					if(is_dir($nowentry)) {
						$q->push($nowentry);
						$dircnt++;
					} elseif (is_file($nowentry) and self::isAllowedExt($nowentry)) {
						$imgcnt++;
						$imgsize += filesize($nowentry);
					}
				}
				if ($dircnt == 0 and $imgcnt > 0) {
					$res[] = array('path' => $nowpath, 'imgcnt' => $imgcnt, 'imgsize' => $imgsize);
				}
			}
			return $res;
		}
		
		public static function GetEntry(string $path): array {
			$handle = opendir($path);
			$rtnval = array();

			while ($entryname = readdir($handle)) {
				if(\mb_substr($entryname, 0, 1) == '.') continue;
				$entryname = $path.'/'.$entryname;
				if(is_dir($entryname) or self::isAllowedExt($entryname)) $rtnval[] = $entryname;
			}
			closedir($handle);
			return $rtnval;
		}
		
		private static function isAllowedExt(string $path): bool {
			$ext = pathinfo($path)['extension'];
			$res = FALSE;
			foreach(config::ALLOWED_EXTENSION as $nowext) {
				if ($nowext == $ext) $res = TRUE;
			}
			return $res;
		}
	}
?>