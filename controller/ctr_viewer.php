<?php
	require_once 'config/config.php';
	require_once 'model/mdl_book.php';
	require_once 'model/library.php';

	class ctr_viewer {
		public static function ShowImage($bookid, $pagenum) {
			if(empty($pagenum) or is_int($pagenum)){
				echo '<img class="filled-image" onclick=location.href="./viewer.php?book_id='.$bookid.'&page=2" src=./image.php?book_id='.$bookid.'&page=1>';
			} else {
				if(!mdl_book::InitSqlite()) return;
				$res = mdl_book::SearchBook($bookid);
				mdl_book::CloseSqlite();
				if (count($res) == 0) {
					echo '404 Not Found';
				} else {
					$pages = library::GetEntry($res[0]->path);
					if(count($pages) == (int)$pagenum){
						echo '<img class="filled-image" src=./image.php?book_id='.$bookid.'&page='.$pagenum.'>';
					} else {
						echo '<img class="filled-image" onclick=location.href="./viewer.php?book_id='.$bookid.'&page='.($pagenum + 1).'" src=./image.php?book_id='.$bookid.'&page='.$pagenum.'>';
					}
				}
			}
		}
		
		public static function ShowInfo($book_id, $pagenum) {
			if(!mdl_book::InitSqlite()) return;
			$res = mdl_book::SearchBook($book_id);
			mdl_book::CloseSqlite();

			if (count($res) == 0) {
				$txt = '404';
			} else {
				$txt = $pagenum.'/'.(string)count(library::GetEntry($res[0]->path));
			}
			
			echo '<div class="book-info">'.$txt.'</div>';
		}
	}
?>