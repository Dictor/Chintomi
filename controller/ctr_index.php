<?php
    require_once 'model/mdl_user.php';

    class ctr_index {
        public function CheckUser($sess_uname, $user_name, $user_pass){
            if (!empty($sess_uname)) {
				echo '<script>location.href="./list.php";</script>';
			} else {
				if (empty($user_name) or empty($user_pass)) {
					//최초 접속 (아무것도 처리하지 않음)
				} else {
					if (mdl_user::CheckPassword($user_name, $user_pass)) {
					    $sess_uname = $user_name;
					    echo '<script>location.href="./list.php";</script>';
					} else {
					    echo '<script>alert("일치하는 자격증명이 없습니다!");</script>';
					}
				}
			}
        }
    }
?>