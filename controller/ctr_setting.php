<?php
	namespace Dictor\Chintomi;
	require_once 'autoload.php';
	
	class SettingMenu {
	    public $menu_name, $handler, $permission;
	    public function __construct(string $menuname, string $handlername, int $permission) {
	        $this->menu_name = $menuname;
	        $this->handler = $handlername;
	        $this->permission = $permission;
	    }
	}
	
	class ctr_setting {
		public static $SettingMenu = array();
		
		public static function InitMenu() {
			self::$SettingMenu = array(	
									'change_pw' => new SettingMenu("비밀번호 변경", "sub_setting_changepw", 0), 
									'view_config' => new SettingMenu("환경 설정", "sub_setting_viewconfig", 999), 
									'update_library' => new SettingMenu("라이브러리 갱신", "sub_setting_updatelibrary", 999),
									'view_library' => new SettingMenu("라이브러리 관리", "sub_setting_viewlibrary", 999),
									'update_user' => new SettingMenu("사용자 관리", "sub_setting_updateuser", 999)
								);	
		}
		
	    public static function DisplayNavbarItem($uname) {
	        foreach (self::$SettingMenu as $nowmenu) {
	            if ($nowmenu->permission <= mdl_user::GetPermission($uname)) {
	            	echo '<li class="nav-item active">';
	            	echo '<a class="nav-link" href="'.utl_htmldoc::GetHrefPath('PAGE_SETTING').'/'.$nowmenu->handler.'">'.$nowmenu->menu_name.'</a>';
	            	echo '</li>';
	            }
	        }
	    }
	    
	    public static function spDisplayConfig() {
	    	foreach (config::GetMember() as $k => $v) {
	    		echo '<tr><th scope="row">'.$k.'</th><td>'.var_export($v, TRUE).'</td></tr>';
	    	}
	    }
	}
?>