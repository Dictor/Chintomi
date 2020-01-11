<?php
	namespace Dictor\Chintomi;
	require_once 'autoload.php';
	
	class ctr_list {
		public static function GetBooks() {
			if(mdl_book::UseDB() == 0){
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
					echo '<div class="list-title"><p>'.$books[$i]->name.'</p><p><span class="badge badge-info">'.$books[$i]->imgcnt.'장 / '.mdl_book::GetHumanFileSize($books[$i]->imgsize).'</span><span class="badge badge-light">'.(new \DateTime($books[$i]->added_date))->format('y/m/d H:i').'</span></p></div></a>';
				}
				self::ShowPage(1, floor(count($books) / config::LIST_PAGIGATION_THRESHOLD) + 1, $pagenum);
			} else {
				foreach($books as $nowbook) {
					echo '<a href="javascript:go_viewer('.$nowbook->id.')" class="list-group-item list-group-item-action">';
					if (config::THUMBNAIL_DISPLAY_ENABLE) echo self::ShowThumbnail($nowbook->id);
					echo '<div class="list-title"><p>'.$nowbook->name.'</p><p><span class="badge badge-info">'.$nowbook->imgcnt.'장 / '.mdl_book::GetHumanFileSize($nowbook->imgsize).'</span><span class="badge badge-light">'.(new \DateTime($nowbook->added_date))->format('y/m/d H:i').'</span></p></div></a>';
				}	
			}
		}
		
		public static function ShowToolbar(int $bookcnt, string $username) {
			echo '<div class="toolbar">';
			echo '<div class="btn-group btn-logout"><button class="btn btn-light btn-sm dropdown-toggle" type="button" data-toggle="dropdown"><span class="service-icon"><i class="icon-user"></i></span> '.$username.'</button>';
			echo '<div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="javascript:go_setting()">관리</a><a class="dropdown-item" href="javascript:logout()">로그아웃</a></div></div>';
			echo '<span class="list-summary">총 '.(string)$bookcnt.'개의 결과</span>';
			echo '<div class="search-form input-group"><input type="text" class="form-control"><div class="input-group-append"><button class="btn btn-outline-secondary" type="button">검색</button></div></div>';
			echo '</div>';
		}

		public static function ShowPage($pfirst, $plast, $pnow){
			echo '<nav aria-label="Page navigation" class="list-pagination nav-center"><ul class="pagination ul-center"><li class="page-item"><a class="page-link" href="javascript:go_list('.(string)$pfirst.')" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
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
				return '<div class="thumbnail list-therr"><span class="service-icon"><i class="icon-exclamation"></i></span></div>';
			} else {
				return '<img class="thumbnail" src="'.ctr_viewer::MakeBase64Image($thumbpath).'">';
			}
		}
	}
?>