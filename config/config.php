<?php
	namespace Dictor\Chintomi;

	class config {
		/*
		[FILE SETTING]
		PATH_COMICBOOK		: Path of Comicbook directory
		ALLOWED_EXTENSION	: Extension of allowed image file
		*/
		const PATH_COMICBOOK = "/chintomi/books";
		const ALLOWED_EXTENSION = array("jpg", "jpeg", "png", "bmp", "gif");

		/*
		[DATABASE_SETTING]
		DB_HANDLER			: Database handler name using in chintomi
								* SQLITE : sqlite3
								* JSON : json based database (DEFAULT)
		PATH_SQLITE			: Path of sqlite DB file
		*/
		const DB_HANDLER = "JSON";
		const PATH_SQLITE = "/chintomi/library";
		const PATH_JSON = "/chintomi/library";
		
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
		- User who has more than each level input can access each function.
		PERMISSION_LEVEL_ADMIN	: Permission of super admin who can access admin page.
		PERMISSION_LEVEL_VIEWER	: Permission of user who can access viewer page.
		PERMISSION_LEVER_LIST	: Permission of user who can access list page.
		*/
		const PERMISSION_LEVEL_ADMIN = 999;
		const PERMISSION_LEVEL_VIEWER = 2;
		const PERMISSION_LEVEL_LIST = 2;

		/*
		[INPUT VALIDATION SETTING]
		INPUT_VALIDATION_USERNAME	: Regular expression used for user name string validation
		INPUT_VALIDATION_PASSWORD	: Regular expression used for password string validation
		*/
		const INPUT_VALIDATION_USERNAME = '/^[A-Za-z0-9]{4,12}$/';
		const INPUT_VALIDATION_PASSWORD = '/^.*(?=^.{4,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/';

		/*
		[LIST PAGINATION SETTING]
		LIST_PAGIGATION_ENABLE		: When this is true, pagination function in 'list.php' is enabled.
		LIST_PAGIGATION_THRESHOLD	: When book amount exceeds this value, book list is paginated per this value.
		*/
		const LIST_PAGIGATION_ENABLE = TRUE;
		const LIST_PAGIGATION_THRESHOLD = 15;

		/*
		[THUMBNAIL SETTING]
		THUMBNAIL_DISPLAY_ENABLE	: When this is true, thumbnail will display on list page
		THUMBNAIL_LONGSIDE_LENGTH	: Pixel length of thumbnail's longside
		THUMBNAIL_QUALITY			: Quality percentage of thumbnail
		*/
		const THUMBNAIL_DISPLAY_ENABLE = TRUE;
		const THUMBNAIL_LONGSIDE_LENGTH = 150;
		const THUMBNAIL_QUALITY = 85;

		/*
		[URL SETTING]
		URL_SUBPATH_ENABLE	: Serve resources with specific sub path, using for situation like behind reverse proxy.
		URL_SUBPATH 		: Subpath string, didn't need trailing slash.
		URLREWRITE_ENABLE	: TRUE = (Only for URL Rewrite enabled WAS) URL is formatted shortly and beautiful (It require URL rewrite through rule defined in .htaccess)
					  FALSE	= (for URL Rewrite disabled or not supported WAS) URL isn't formatted shortly
		*/
		const URL_SUBPATH_ENABLE = FALSE;
		const URL_SUBPATH = "/chintomi";
		const URLREWRITE_ENABLE = FALSE;
		
		/*
		[ADVANCE SETTING]
		MEMORY_UNLIMIT_UPDATE_THUMBNAIL	: If chintomi died with out of memory error, you can try turn on this option.
		*/
		const MEMORY_UNLIMIT_UPDATE_THUMBNAIL = FALSE;

		public static function GetMember() {
			$refl = new \ReflectionClass(get_called_class());
			return $refl->getConstants();
		}
	}


?>
