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
	    public static function DisplayNavbarItem($uname) {
	    	$pages = array('changepw' => new SettingMenu("비밀번호 변경", "page_changepw", 0), 'library' => new SettingMenu("라이브러리 관리", "page_library", 999)); //이거 어카누....
	        foreach ($pages as $nowmenu) {
	            if ($nowmenu->permission <= mdl_user::GetPermission($uname)) {
	            	echo '<li class="nav-item active">';
	            	echo '<a class="nav-link" href="#">'.$nowmenu->menu_name.'</a>';
	            	echo '</li>';
	            }
	        }
	    }
	}
?>