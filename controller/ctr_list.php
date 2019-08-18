<?php
	namespace Dictor\Chintomi;
	require_once 'autoload.php';
	
	class ctr_list {
		public static function GetBooks() {
			if(mdl_book::UseDB() == 0){
				mdl_library::UpdateLibrary(); //나중에 무조건이 아니라 시간간격으로 업데이트 하게 수정!
				mdl_library::UpdateThumbnail();
				return mdl_book::GetAllBooks();
			} else {
				utl_htmldoc::ShowError(500, "DB Error");
				return NULL;
			}
		}
		
		public static function DisplayBooks(array $books, int $pagenum, string $username) {
			self::ShowToolbar(count($books), $username);
			if (config::LIST_PAGIGATION_ENABLE){
				$startnum = ($pagenum - 1) * config::LIST_PAGIGATION_THRESHOLD;
				$endnum = $pagenum * config::LIST_PAGIGATION_THRESHOLD - 1;
				if($startnum >= count($books)){
					utl_htmldoc::ShowError(404, "Page Not Found");
					utl_htmldoc::CloseDocument();
					return;
				}
				if($endnum >= count($books)) $endnum = count($books) - 1;
				for($i = $startnum; $i <= $endnum; $i++){
					echo '<a href="javascript:go_viewer('.$books[$i]->id.')" class="list-group-item list-group-item-action">';
					if (config::THUMBNAIL_DISPLAY_ENABLE) echo self::ShowThumbnail($books[$i]->id);
					echo '<div class="list-title">'.$books[$i]->name.'</div></a>';
				}
				self::ShowPage(1, floor(count($books) / config::LIST_PAGIGATION_THRESHOLD) + 1, $pagenum);
			} else {
				foreach($books as $nowbook) {
					echo '<a href="javascript:go_viewer('.$nowbook->id.')" class="list-group-item list-group-item-action">';
					if (config::THUMBNAIL_DISPLAY_ENABLE) echo self::ShowThumbnail($nowbook->id);
					echo '<div class="list-title">'.$nowbook->id.'</div></a>';
				}	
			}
		}
		
		public static function ShowToolbar(int $bookcnt, string $username) {
			echo '<div class="toolbar">';
			echo '<div class="btn-group btn-logout"><button class="btn btn-light btn-sm dropdown-toggle" type="button" data-toggle="dropdown"><span class="service-icon"><i class="icon-user"></i></span> '.$username.'</button>';
			echo '<div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="/setting">관리</a><a class="dropdown-item" href="/list/action/logout">로그아웃</a></div></div>';
			echo '<span class="list-summary">총 '.(string)$bookcnt.'개의 결과</span>';
			echo '</div>';
		}
		
		public static function ProcessAction(string $name) {
			if ($name == 'logout') {
				unset($_SESSION['uname']);
				echo '<script>location.href = "/";</script>';
			}
		}
		
		public static function ShowPage($pfirst, $plast, $pnow){
			echo '<nav aria-label="Page navigation" class="list-pagination"><ul class="pagination justify-content-center"><li class="page-item"><a class="page-link" href="javascript:go_list('.(string)$pfirst.')" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
			for($i = $pfirst; $i <= $plast; $i++){
				if($i == $pnow){
					echo '<li class="page-item active" aria-current="page"><a class="page-link" href="javascript:go_list('.(string)$i.')">'.(string)$i.' <span class="sr-only">(current)</span></a></li>';
				} else {
					echo '<li class="page-item"><a class="page-link" href="javascript:go_list('.(string)$i.')">'.(string)$i.'</a></li>';
				}
			}
			echo '<li class="page-item"><a class="page-link" href="javascript:go_list('.(string)$plast.')" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li></ul></nav>';
		}
		
		public static function ShowThumbnail(string $bookid) {
			if(mdl_book::UseDB() != 0) return;
			$thumbpath = mdl_library::$thumbdir.'/'.$bookid.'.jpg';
			if (!is_file($thumbpath)) {
				Util::ShowError(404, "Requested thumbnail not founded");
			} else {
				return '<img class="thumbnail" src="'.ctr_viewer::MakeBase64Image($thumbpath).'">';
			}
		}
	}
?>