<?php
	class ctr_list {
		public static function DisplayDirectory(array $pathlist) {
			foreach($pathlist as $nowpath) {
				print '<a href="#" class="list-group-item list-group-item-action">'.$nowpath.'</a>';
			}
		}
		
		public static function DisplayBooks(array $books) {
			echo "총 ".count($books)."개의 결과";
			foreach($books as $nowbook) {
				print '<a href="javascript:go_viewer('.$nowbook->id.')" class="list-group-item list-group-item-action">'.$nowbook->name.'</a>';
			}
		}
	}
	
?>