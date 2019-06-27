<?php
    require_once 'adapter/DBhandler.php';
    require_once 'adapter/library.php';
    
    class mdl_user {
        public static function CheckPassword($userName, $userPass) {
            hndSQLite::Open(config::PATH_SQLITE);
            $res = hndSQLite::ResultToComicbook(hndSQLite::Query('SELECT * FROM user WHERE user_name=:uname', array('uname' => $userName)));
            if (!is_null($res) and count($res) != 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }
?>