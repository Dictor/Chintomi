<?php
    require_once 'model/mdl_user.php';
    require_once 'util/util.php';
    require_once 'config/config.php';

    class ctr_index {
        public function CheckUser($user_name, $user_pass){
            if (!empty((array_key_exists('uname', $_SESSION) ? $_SESSION['uname'] : NULL))) {
				echo '<script>location.href="./list.php";</script>';
			} else {
				if (empty($user_name) or empty($user_pass)) {
					//최초 접속 (아무것도 처리하지 않음)
				} else {
					if (!preg_match(Config::INPUT_VALIDATION_USERNAME, $user_name) or !preg_match(Config::INPUT_VALIDATION_PASSWORD, $user_pass)){
						Util::ShowError(400, "Invalid user name or password");
						Util::CloseDocument();
					} else {
						if (mdl_user::UseDB() != 0) {
							Util::ShowError(500, "DB Error");
							Util::CloseDocument();
						} else {
							if (mdl_user::CheckPassword($user_name, $user_pass)) {
							    $_SESSION['uname'] = $user_name;
							    echo '<script>location.href="./list.php";</script>';
							} else {
							    echo '<script>alert("일치하는 자격증명이 없습니다!");</script>';
							}
						}
					}
				}
			}
        }
    }
?>