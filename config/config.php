<?php
	class Config {
		/*
		[FILE SETTING]
		PATH_COMICBOOK		: Path of Comicbook directory
		PATH_SQLITE 		: Path of sqlite DB file
		ALLOWED_EXTENSION	: Extension of allowed image file
		*/
		const PATH_COMICBOOK = "/home/ubuntu/environment/Chintomi/data";
		const PATH_SQLITE = "/home/ubuntu/environment/Chintomi/data/chintomi.db";
		const ALLOWED_EXTENSION = array("jpg", "jpeg", "png", "bmp", "gif");
		
		/*
		[IMAGE RESIZE SETTING]
		RESIZEIMG_ENABLE	: TRUE = Enable resizing when image's longer length is over resizeImageSize
							  FALSE = Disable resizing
							  (Resizing image expends TTFB due to resizing process time. So, enable it when necessary situation like low-bandwidth network.)
		RESIZEIMG_THRESHOLD : Set threshold size of resizing 
		*/
		const RESIZEIMG_ENABLE = FALSE;
		const RESIZEIMG_THRESHOLD = 1200;
		
		/*
		[PERMISSION SETTING]
		* User who has more than each level input can access each function.
		PERMISSION_LEVEL_ADMIN	: Permission of super admin who can access admin page. 
		PERMISSION_LEVEL_VIEWER	: Permission of user who can access viewer page.
		PERMISSION_LEVER_LIST	: Permission of user who can access list page. 
		*/
		const PERMISSION_LEVEL_ADMIN = 999;
		const PERMISSION_LEVEL_VIEWER = 2;
		const PERMISSION_LEVEL_LIST = 2;
	}
?>