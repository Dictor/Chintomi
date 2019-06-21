<?php
	require 'config/config.php';

	class ctr_viewer {
		public static function ShowImage(string $bookid, $pagenum) {
			if(is_null($pagenum)){
				echo '<img class="filled-image" onclick="./viewer.php?book_id='.$bookid.'&page=2" src="./image.php?book_id='.$bookid.'&page=1">';
			} else {
				echo '<img class="filled-image" onclick=location.href="./viewer.php?book_id='.$bookid.'&page='.($pagenum + 1).'" src=./image.php?book_id='.$bookid.'&page='.$pagenum.'>';
			}
		}	
	}
?>