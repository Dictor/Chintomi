<?php
	require_once 'model/mdl_book.php';
	require_once 'model/mdl_user.php';
	require_once 'adapter/library.php';

	class ctr_list {
		public static function CheckPermission() {
			if (mdl_user::UseDB() != 0) {
				echo "DB Error!";
			} else {
				if (array_key_exists('uname', $_SESSION)) {
					if (mdl_user::GetPermission($_SESSION['uname']) >= Config::PERMISSION_LEVEL_LIST){
						return TRUE;
					} else {
						return FALSE;
					}
				} else {
					return FALSE;
				}
			}
		}
		
		public static function GetBooks() {
			if(mdl_book::UseDB() == 0){
				library::UpdateLibrary(); //나중에 무조건이 아니라 시간간격으로 업데이트 하게 수정!
				return mdl_book::GetAllBooks();
			} else {
				echo "DB Error!";
				return NULL;
			}
		}
		
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