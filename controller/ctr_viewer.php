<?php
	require 'config/config.php';

	class ctr_viewer {
		public static function ShowImage(string $bookid, $pagenum) {
			if(is_null($pagenum)){
				echo '<img src=./image.php?book_id='.$bookid.'&page=1>';
			} else {
				echo '<img src=./image.php?book_id='.$bookid.'&page='.$pagenum.'>';
			}
		}	
	}
?>