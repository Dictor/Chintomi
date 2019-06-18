<?php 
//파일 경로 취약점 처리 해야함! 지금은 일단 기능만 구현
	require 'config/config.php';
	header('Content-Type: image/'.$_GET['ext']); 
	$file_path = Config::dataPath.$_GET['n'];
	$fp = fopen($file_path, 'r');
	$arr = fread($fp, filesize($file_path));
	echo $arr; 
	fclose($fp);
?>