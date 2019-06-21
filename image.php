<?php 
//파일 경로 취약점 처리 해야함! 지금은 일단 기능만 구현
//GET 파라미터 : book_id, page
	require_once 'config/config.php';
	require_once 'model/mdl_book.php';
	require_once 'model/library.php';

	$res = mdl_book::InitSqlite();
	if ($res != 0){
		echo "DB Error : ".$res;
		return;
	}
	$res = mdl_book::SearchBook($_GET['book_id']);
	mdl_book::CloseSqlite();
	if (count($res) == 0) {
		//검색 결과 없음
		SendImageBinary(config::notFoundimgPath);
	} else {
		$pages = library::GetEntry($res[0]->path);
		sort($pages);
		if((count($pages) >= (int)$_GET['page']) and ((int)$_GET['page'] >= 1)){
			SendImageBinary($pages[$_GET['page'] - 1]);
		} else {
			SendImageBinary(config::notFoundimgPath);
		}
	}
	
	function SendImageBinary($path) {
		header('Content-Type: image/'.pathinfo($path)['extension']);
		header("Content-Length: " . filesize($path));
		$fp = fopen($path, 'r');
		fpassthru($fp);
		fclose($fp);
	}
?>