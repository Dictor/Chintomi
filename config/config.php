<?php
	class Config {
		/*
		[Path Setting]
		dataPath :	Path of Comicbook directory
		sqlitePath :	Path of sqlite DB file
		allowedExt :	Extension of allowed image file
		*/
		const dataPath = "/home/ubuntu/environment/Chintomi/data";
		const sqlitePath = "/home/ubuntu/environment/Chintomi/model/chintomi.db";
		const allowedExt = array("jpg", "jpeg", "png", "bmp", "gif");
		
		/*
		[Image Resize Setting]
		resizeImage :	TRUE = Enable resizing when image's longer length is over resizeImageSize
						FALSE = Disable resizing
		resizeImageSize :	Set threshold size of resizing 
		*/
		const resizeImage = TRUE;
		const resizeImageSize = 1200;
	}
?>