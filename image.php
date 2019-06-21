<?php 
//파일 경로 취약점 처리 해야함! 지금은 일단 기능만 구현
//GET 파라미터 : book_id, page
	require_once 'config/config.php';
	require_once 'model/mdl_book.php';
	require_once 'model/library.php';
	
	//header('Content-Type: image/'.$_GET['ext']); 
	$res = mdl_book::SearchBook($_GET['book_id']);
	if (count($res) == 0) {
		//검색 결과 없음
		echo "404";
	} else {
		$pages = library::GetEntry($res->book_path);
		echo GetImageBinary($pages[$_GET['page']]);
	}
	
	function GetImageBinary($path) {
		$fp = fopen($file_path, 'r');
		$arr = fread($fp, filesize($file_path));
		fclose($fp);
		return $arr; 
	}
?>