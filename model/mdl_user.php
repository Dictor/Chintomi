<?php
    namespace Dictor\Chintomi;
    require_once 'autoload.php';
    
    class mdl_user {
        public static function UseDB(): int {
            switch (config::DB_HANDLER) {
                case 'SQLITE': return hnd_SQLite::Open(config::PATH_SQLITE);
            }
		}
        
        public static function CheckPassword($userName, $userPass): bool {
            $res = [];
            switch (config::DB_HANDLER) {
                case 'SQLITE': $res = hnd_SQLite::ResultToArray(hnd_SQLite::Query('SELECT * FROM user WHERE user_name=:uname', array('uname' => $userName))); break;
            }
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
        
        public static function ChangePassword($userName, $newPass): void {
            switch (config::DB_HANDLER) {
                case 'SQLITE': hnd_SQLite::Execute('UPDATE user SET user_pass = :newpass WHERE user_name = :uname', array('uname' => $userName, 'newpass' => password_hash($newPass, PASSWORD_DEFAULT))); break;   
            }
        }
        
        public static function ChangePermission($uname, $newper): void {
            switch (config::DB_HANDLER) {
                case 'SQLITE': hnd_SQLite::Execute('UPDATE user SET user_permission = :uper WHERE user_name = :uname', array('uname' => $uname, 'uper' => $newper)); break;
            }
        }
        
        public static function DeleteUser($userName): void {
            switch (config::DB_HANDLER) {
                case 'SQLITE': hnd_SQLite::Execute('DELETE FROM user WHERE user_name = :uname', array('uname' => $userName)); break;
            }
        }
        
        public static function MakeUser($uname, $upass, $uper): void {
            switch (config::DB_HANDLER) {
                case 'SQLITE': hnd_SQLite::Execute('INSERT INTO user VALUES (:uname, :upass, :uper);', array('uname' => $uname, 'upass' => password_hash($upass, PASSWORD_DEFAULT), 'uper' => $uper)); break;
            }
        }
        
        public static function GetAllUser(): array {
            $res = [];
            switch (config::DB_HANDLER) {
                case 'SQLITE': $res = hnd_SQLite::ResultToArray(hnd_SQLite::Query('SELECT * FROM user', array())); break;
            }
            $rtn = array();
            foreach ($res as $nowuser) {
                $rtn[$nowuser['user_name']] = $nowuser['user_permission'];
            }
            return $rtn;
        }
        
        public static function CheckAdminExist(): ?bool {
            $res = NULL;
            switch (config::DB_HANDLER) {
                case 'SQLITE': $res = hnd_SQLite::Query('SELECT * FROM user WHERE user_permission=:uper', array('uper' => config::PERMISSION_LEVEL_ADMIN)); break;
            }
            if(is_null($res)){
                return NULL;
            } else if (count(hnd_SQLite::ResultToArray($res)) == 0) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
        
        public static function MakeAdmin($uname, $upass): bool {
            if (!self::CheckAdminExist()) {
                switch (config::DB_HANDLER) {
                    case 'SQLITE': 
                        self::MakeUser($uname, $upass, Config::PERMISSION_LEVEL_ADMIN); 
                        return TRUE;
                }
            }
            return FALSE;
        }
        
        public static function GetPermission($uname): ?int {
            $res = [];
            switch (config::DB_HANDLER) {
                case 'SQLITE': $res =  hnd_SQLite::ResultToArray(hnd_SQLite::Query('SELECT * FROM user WHERE user_name=:uname', array('uname' => $uname))); break;
            }
            if(is_null($res) or count($res) == 0){
                return NULL;
            } else {
                return (int)$res[0]['user_permission'];
            }
        }
        
        public static function CheckPermission($minlv): ?bool {
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