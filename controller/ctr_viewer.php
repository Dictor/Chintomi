<?php
	require_once 'config/config.php';
	require_once 'model/mdl_book.php';
	require_once 'model/mdl_user.php';
	require_once 'util/library.php';
	require_once 'util/util.php';
	require_once 'vendor/autoload.php';
	use \Gumlet\ImageResize;

	class ctr_viewer {
		private static $lastResized = FALSE;
		
		public static function CheckPermission() {
			if (mdl_user::UseDB() != 0) {
				Util::ShowError(500, "DB Error");
			} else {
				if (array_key_exists('uname', $_SESSION)) {
					if (mdl_user::GetPermission($_SESSION['uname']) >= Config::PERMISSION_LEVEL_VIEWER){
						return TRUE;
					} else {
						return FALSE;
					}
				} else {
					return FALSE;
				}
			}
		}
		
		public static function ShowImage(string $bookid, string $pagenum) {
			if(empty($pagenum) or is_int($pagenum)){
				echo '<img class="filled-image" onclick=location.href="./viewer.php?book_id='.$bookid.'&page=2" src=./image.php?book_id='.$bookid.'&page=1>';
			} else {
				if(mdl_book::UseDB() != 0) return;
				$res = mdl_book::SearchBook($bookid);
				if (count($res) == 0) {
					Util::ShowError(404, "Requested image not founded");
				} else {
					$pages = library::GetEntry($res[0]->path);
					if (count($pages) < (int)$pagenum or (int)$pagenum < 0) {
						Util::ShowError(404, "Requested image not founded");
					} else if(count($pages) == (int)$pagenum){
						echo '<img class="filled-image" src="'.self::MakeBase64Image(self::GetImagePath($pages, (int)$pagenum)).'">';
						self::ShowInfo($pages, (int)$pagenum);
					} else {
						echo '<img class="filled-image" onclick=location.href="./viewer.php?book_id='.$bookid.'&page='.((int)$pagenum + 1).'" src="'.self::MakeBase64Image(self::GetImagePath($pages, (int)$pagenum)).'">';
						self::ShowInfo($pages, (int)$pagenum);
					}
				}
			}
		}
		
		public static function ShowInfo(array $pages, int $pagenum) {
			$txt = $pagenum.'/'.(string)count($pages);
			if (self::$lastResized) $txt = $txt.'<br><p>리사이즈 됨</p>';
			echo '<div class="book-info">'.$txt.'</div>';
		}
		
		public static function ShowPageDropdown($bookid, $pfirst, $pnow, $plast) {
			echo '<div class="btn-group"><button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
			echo (string)$pnow;
			echo '</button><div class="dropdown-menu">';
			for ($i = $pfirst; $i <= $plast; $i++){
				if ($i == $pnow) {
					echo '<a class="dropdown-item active" href="./viewer.php?book_id='.$bookid.'&page='.(string)$i.'">'.$i.'</a>';
				} else {
					echo '<a class="dropdown-item" href="./viewer.php?book_id='.$bookid.'&page='.(string)$i.'">'.$i.'</a>';
				}
			}
			echo '</div></div>';
		}
		
		public static function GetImagePath(array $pages, int $pagenum) {
			sort($pages);
			if((count($pages) >= $pagenum) and ($pagenum >= 1)){
				return $pages[$pagenum - 1];
			} else {
				return NULL;
			}
		}
		
		private static function MakeBase64Image($path){
			if (config::RESIZEIMG_ENABLE and (getimagesize($path)[0] > config::RESIZEIMG_THRESHOLD or getimagesize($path)[1] > config::RESIZEIMG_THRESHOLD)){
				$image = new ImageResize($path);
				$image->resizeToLongSide(config::RESIZEIMG_THRESHOLD);
				self::$lastResized = TRUE;
				return 'data:image/'.pathinfo($path)['extension'].';base64,'.base64_encode($image->getImageAsString());
			} else {
				self::$lastResized = FALSE;
				return 'data:image/'.pathinfo($path)['extension'].';base64,'.base64_encode(file_get_contents($path));
			}
		}
	}
?>