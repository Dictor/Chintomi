<?php
    namespace Dictor\Chintomi;
    require_once 'autoload.php';
    
    class mdl_user {
        public static function UseDB() {
            return hnd_SQLite::Open(config::PATH_SQLITE);
		}
        
        public static function CheckPassword($userName, $userPass) {
            $res = hnd_SQLite::ResultToArray(hnd_SQLite::Query('SELECT * FROM user WHERE user_name=:uname', array('uname' => $userName)));
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
        
        public static function ChangePassword($userName, $newPass) {
            return hnd_SQLite::Execute('UPDATE user SET user_pass = :newpass WHERE user_name = :uname', array('uname' => $userName, 'newpass' => password_hash($newPass, PASSWORD_DEFAULT)));    
        }
        
        public static function DeleteUser($userName) {
            return hnd_SQLite::Execute('DELETE FROM user WHERE user_name = :uname', array('uname' => $userName)); 
        }
        
        public static function MakeUser($uname, $upass, $uper) {
            return hnd_SQLite::Execute('INSERT INTO user VALUES (:uname, :upass, :uper);', array('uname' => $uname, 'upass' => password_hash($upass, PASSWORD_DEFAULT), 'uper' => $uper));
        }
        
        public static function GetAllUser() {
            $res = hnd_SQLite::ResultToArray(hnd_SQLite::Query('SELECT * FROM user', array()));
            $rtn = array();
            foreach ($res as $nowuser) {
                $rtn[$nowuser['user_name']] = $nowuser['user_permission'];
            }
            return $rtn;
        }
        
        public static function CheckAdminExist() {
            $res = hnd_SQLite::Query('SELECT * FROM user WHERE user_permission=:uper', array('uper' => config::PERMISSION_LEVEL_ADMIN));
            if(is_null($res)){
                return NULL;
            } else if (count(hnd_SQLite::ResultToArray($res)) == 0) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
        
        public static function MakeAdmin($uname, $upass) {
            if (!self::CheckAdminExist()) return MakeUser($uname, $upass, Config::PERMISSION_LEVEL_ADMIN);
        }
        
        public static function GetPermission($uname) {
            $res = hnd_SQLite::ResultToArray(hnd_SQLite::Query('SELECT * FROM user WHERE user_name=:uname', array('uname' => $uname)));
            if(is_null($res) or count($res) == 0){
                return FALSE;
            } else {
                return (int)$res[0]['user_permission'];
            }
        }
        
        public static function CheckPermission($minlv) {
			if (mdl_user::UseDB() != 0) {
				utl_htmldoc::ShowError(500, "DB Error");
				return NULL;
			} else {
				if (array_key_exists('uname', $_SESSION)) {
					if (mdl_user::GetPermission($_SESSION['uname']) >= $minlv){
						return TRUE;
					} else {
						return FALSE;
					}
				} else {
					return FALSE;
				}
			}
		}
    }
?>