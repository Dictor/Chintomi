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
                        if (array_key_exists('nowpass', $_POST) && array_key_exists('newpass', $_POST)) {
                            if(!preg_match(Config::INPUT_VALIDATION_PASSWORD, $_POST['nowpass']) or !preg_match(Config::INPUT_VALIDATION_PASSWORD, $_POST['newpass'])){
                                echo json_encode(array('res' => 'error', 'msg' => 'input invalid'));  
                            } else {
                                if (mdl_user::CheckPassword($_SESSION['uname'], $_POST['nowpass'])) {
                                    mdl_user::ChangePassword($_SESSION['uname'], $_POST['newpass']);
                                    echo json_encode(array('res' => 'success'));  
                                } else {
                                    echo json_encode(array('res' => 'error', 'msg' => 'now pw incorrect'));  
                                } 
                            }
                        } else {
                            echo json_encode(array('res' => 'error', 'msg' => 'invalid params'));
                        }
                    } else {
                        echo json_encode(array('res' => 'error', 'msg' => 'no authority'));
                    }
                    break;
                case 'delete_user':
                    if (mdl_user::CheckPermission(config::PERMISSION_LEVEL_ADMIN)) {
                        if (array_key_exists('uname', $_POST)) {
                            if(mdl_user::GetPermission($_POST['uname']) == FALSE) {
                                echo json_encode(array('res' => 'error', 'msg' => 'invalid user name'));
                            } else if (mdl_user::GetPermission($_POST['uname']) >= config::PERMISSION_LEVEL_ADMIN) {
                                echo json_encode(array('res' => 'error', 'msg' => 'cant delete admin account'));
                            } else {
                                mdl_user::DeleteUser($_POST['uname']);
                                echo json_encode(array('res' => 'success'));
                            }
                        } else {
                            echo json_encode(array('res' => 'error', 'msg' => 'invalid params'));
                        }
                    } else {
                        echo json_encode(array('res' => 'error', 'msg' => 'no authority'));
                    }
                    break;
                case 'make_user':
                    if (mdl_user::CheckPermission(config::PERMISSION_LEVEL_ADMIN)) {
                        if (array_key_exists('uname', $_POST) && array_key_exists('upass', $_POST) && array_key_exists('uper', $_POST)) {
                            if(!preg_match(Config::INPUT_VALIDATION_USERNAME, $_POST['uname']) or !preg_match(Config::INPUT_VALIDATION_PASSWORD, $_POST['upass']) or (int)$_POST['uper'] < 0 or (int)$_POST['uper'] > 999){
                                echo json_encode(array('res' => 'error', 'msg' => 'input invalid'));  
                            } else {
                                if(mdl_user::GetPermission($_POST['uname']) == FALSE) {
                                    mdl_user::MakeUser($_POST['uname'], $_POST['upass'], $_POST['uper']);
                                    echo json_encode(array('res' => 'success'));
                                } else {
                                    echo json_encode(array('res' => 'error', 'msg' => 'already exist user'));
                                }
                            }
                        } else {
                            echo json_encode(array('res' => 'error', 'msg' => 'invalid params'));
                        }
                    } else {
                        echo json_encode(array('res' => 'error', 'msg' => 'no authority'));
                    }
                    break;
                default:
                    echo json_encode(array('res' => 'error', 'msg' => 'undefined api verb'));
            }
        }
    }
?>