<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';

    session_start();
	$urlarg = filter_var($_GET['path'], FILTER_SANITIZE_STRING);
	$urlarg = trim($urlarg, '/');
	$urlargs = explode('/', $urlarg);
	if ($urlargs[0] === 'api') {
	    ctr_api::Process($urlargs);
	    exit();
	} else {
	    echo 
<<<HTMLSTART
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>Chintomi</title>
		
		<link href="/style/standard.css" rel="stylesheet">
		<link href="https://fonts.googleapis.com/earlyaccess/nanumgothic.css" rel="stylesheet" type="text/css">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.min.css" rel="stylesheet">
		
		<!--BootStrap-->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	</head>
HTMLSTART;
	    switch ($urlargs[0]) {
            case 'index':
                require 'view/view_index.php';
                break;
            case 'list':
                require 'view/view_list.php';
                break;
            case 'viewer':
                require 'view/view_viewer.php';
                break;
            case 'setup':
                require 'view/view_setup.php';
                break;
            case 'setting':
                require 'view/view_setting.php';
                break;
            default:
                require 'view/view_index.php';
                break;
        }
	}
?>
</html>