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
                        $libres = mdl_library::UpdateLibrary();
                        $thres = mdl_library::UpdateThumbnail();
                        echo json_encode(array('res' => 'success', 'lib_added' => $libres['added'], 'lib_deleted' => $libres['deleted'], 'th_added' => $thres));
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