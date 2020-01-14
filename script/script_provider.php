<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
	
	if (!mdl_user::CheckPermission(0)) {
	    echo '/* NO AUTHORITY */';
	    exit();
	}
	
	$path_list = [
	    'API' => utl_htmldoc::GetHrefPath('PAGE_API'),
	    'LIST' => utl_htmldoc::GetHrefPath('PAGE_LIST'),
	    'VIEWER' => utl_htmldoc::GetHrefPath('PAGE_VIEWER'),
	    'INDEX' => utl_htmldoc::GetHrefPath('PAGE_INDEX'),
	    'SETTING' => utl_htmldoc::GetHrefPath('PAGE_SETTING')
	];
	
	echo 'const API_PATH = '.json_encode($path_list).";\n";
	require 'script/script.js';
?>