<?php
	namespace Dictor\Chintomi;
    require_once 'autoload.php';
    
    class ctr_api {
        public static function Process($urlparam) {
            switch ($urlparam[1]) {
                case 'logout':
                    if (mdl_user::CheckPermission(0)) {
                        unset($_SESSION['uname']);
                        echo json_encode(array('res' => 'success'));
                    } else {
                        echo json_encode(array('res' => 'error', 'msg' => 'no authority'));
                    }
                    break;
                case 'update_all':
                    if (mdl_user::CheckPermission(config::PERMISSION_LEVEL_ADMIN)) {
                        try {
                            $startt = microtime(TRUE);
                            $libres = mdl_library::UpdateLibrary();
                            $thres = mdl_library::UpdateThumbnail();
                            $endt = microtime(TRUE);
                            echo json_encode(array('res' => 'success', 'msg' => (string)(round($endt - $startt, 4)).'sec elapsed!', 'lib_added' => $libres['added'], 'lib_deleted' => $libres['deleted'], 'th_added' => $thres));
                        } catch (\Throwable $t) {
                            echo json_encode(array('res' => 'error', 'msg' => 'error occured during process : '.$t->getMessage()));
                        }
                    } else {
                        echo json_encode(array('res' => 'error', 'msg' => 'no authority'));
                    }
                    break;
                case 'update_lib':
                    if (mdl_user::CheckPermission(config::PERMISSION_LEVEL_ADMIN)) {
                        try {
                            $startt = microtime(TRUE);
                            $libres = mdl_library::UpdateLibrary();
                            $endt = microtime(TRUE);
                            echo json_encode(array('res' => 'success', 'msg' => (string)(round($endt - $startt, 4)).'sec elapsed!', 'lib_added' => $libres['added'], 'lib_deleted' => $libres['deleted']));
                        } catch (\Throwable $t) {
                            echo json_encode(array('res' => 'error', 'msg' => 'error occured during process : '.$t->getMessage()));
                        }
                    } else {
                        echo json_encode(array('res' => 'error', 'msg' => 'no authority'));
                    }
                    break;
                case 'update_th':
                    if (mdl_user::CheckPermission(config::PERMISSION_LEVEL_ADMIN)) {
                         try {
                            $startt = microtime(TRUE);
                            $thres = mdl_library::UpdateThumbnail();
                            $endt = microtime(TRUE);
                            echo json_encode(array('res' => 'success', 'msg' => (string)(round($endt - $startt, 4)).'sec elapsed!', 'th_added' => $thres));
                        } catch (\Throwable $t) {
                            echo json_encode(array('res' => 'error', 'msg' => 'error occured during process : '.$t->getMessage()));
                        }
                    } else {
                        echo json_encode(array('res' => 'error', 'msg' => 'no authority'));
                    }
                    break;
                case 'update_th_next':
                    if (mdl_user::CheckPermission(config::PERMISSION_LEVEL_ADMIN)) {
                         try {
                            $startt = microtime(TRUE);
                            $thres = mdl_library::UpdateThumbnailNext();
                            $endt = microtime(TRUE);
                            echo json_encode(array('res' => 'success', 'msg' => (string)(round($endt - $startt, 4)).'sec elapsed!', 'th_added' => 'id → '.$thres));
                        } catch (\Throwable $t) {
                            echo json_encode(array('res' => 'error', 'msg' => 'error occured during process : '.$t->getMessage()));
                        }
                    } else {
                        echo json_encode(array('res' => 'error', 'msg' => 'no authority'));
                    }
                    break;
                case 'change_pw':
                    if (mdl_user::CheckPermission(0)) {
                        if (array_key_exists($_POST, 'nowpass') && array_key_exists($_POST, 'newpass')) {
                            if (mdl_user::CheckPassword($_SESSION['uname'], $_POST['nowpass'])) {
                                mdl_user::ChangePassword($_SESSION['uname'], $_POST['newpass']);
                            } else {
                                echo json_encode(array('res' => 'error', 'msg' => 'now pw invalid'));  
                            }
                        } else {
                            echo json_encode(array('res' => 'error', 'msg' => 'invalid params'));
                        }
                        
                    } else {
                        echo json_encode(array('res' => 'error', 'msg' => 'no authority'));
                    }
                default:
                    echo json_encode(array('res' => 'error', 'msg' => 'undefined api verb'));
            }
        }
    }
?>