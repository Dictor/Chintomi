<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
	if (!mdl_user::CheckPermission(0)) {
	    echo '/*NO AUTHORITY*/';
	    exit();
	}
	echo 'var api_host = "'.utl_htmldoc::GetHrefPath('PAGE_API').'";';
?>

function GETApiReq(verb, okcb, errorcb) {
    var req = new XMLHttpRequest();
    req.open("GET", api_host + "/" + verb, true);
    req.onload = function() {
        if (req.status == 200) {
<<<<<<< HEAD
            okcb(req.response);
=======
            okcb();
>>>>>>> 334f2c9ab44e7c17d2209e69917f3be78a93f98c
        } else {
            errorcb();
        }
    };
    req.onerror = errorcb();
    req.send();
}

function POSTApiReq(verb, param, okcb, errorcb) {
    var req = new XMLHttpRequest();
    req.open("POST", api_host + "/" + verb, true);
    req.setRequestHeader('Content-Type', 'application/json');
    req.onload = function() {
        if (req.status == 200) {
<<<<<<< HEAD
            okcb(req.response);
=======
            okcb();
>>>>>>> 334f2c9ab44e7c17d2209e69917f3be78a93f98c
        } else {
            errorcb();
        }
    };
    req.onerror = errorcb();
    req.send(param);
}