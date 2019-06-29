<?php
    require_once 'adapter/DBhandler.php';
    require_once 'adapter/library.php';
    
    class mdl_user {
        public static function UseDB() {
			hndSQLite::Open(config::PATH_SQLITE);
		}
        
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
        
        public static function CheckAdminExist() {
            $res = hndSQLite::Query('SELECT * FROM user WHERE user_permission=:uper', array('uper' => 999));
            if(is_null($res)){
                return NULL;
            } else if (count(hndSQLite::ResultToArray($res)) == 0) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
        
        public static function MakeAdmin($uname, $upass) {
            if (!self::CheckAdminExist()) return hndSQLite::Execute('INSERT INTO user VALUES (:uname, :upass, :uper);', array('uname' => $uname, 'upass' => password_hash($upass, PASSWORD_DEFAULT), 'uper' => 999));
        }
        
        public static function GetPermission($uname) {
            $res = hndSQLite::ResultToArray(hndSQLite::Query('SELECT * FROM user WHERE user_name=:uname', array('uname' => $uname)));
            if(is_null($res) or count($res) == 0){
                return FALSE;
            } else {
                return (int)$res[0]['user_permission'];
            }
        }
    }
?>