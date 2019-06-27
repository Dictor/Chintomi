<?php
    require_once 'adapter/DBhandler.php';
    require_once 'adapter/library.php';
    
    class mdl_user {
        public static function CheckPassword($userName, $userPass) {
            hndSQLite::Open(config::PATH_SQLITE);
            $res = hndSQLite::ResultToArray(hndSQLite::Query('SELECT * FROM user WHERE user_name=:uname', array('uname' => $userName)));
            if (!is_null($res) and count($res) != 0) {
                if(password_verify($userPass, $res[0]['user_pass'])) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }
    }
?>