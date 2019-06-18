<?php
	require_once 'config/config.php';
	
	class ctr_list {
		public static function GetDirectory() {
			$handle = opendir(Config::dataPath);
			$rtnval = array();

			while ($filename = readdir($handle)) {
				if($filename == '.' || $filename == '..') continue;
				$filepath = Config::dataPath.$filename;
				if(is_dir($filepath)){
					array_push($rtnval, $filename);
				}
			}
			closedir($handle);
			return $rtnval;
		}

		public static function DisplayDirectory(array $pathlist) {
			foreach($pathlist as $nowpath) {
				print '<a href="#" class="list-group-item list-group-item-action">'.$nowpath.'</a>';
			}
		}
		
		public static function DisplayBooks(array $books) {
			foreach($books as $nowbook) {
				print '<a href="#" class="list-group-item list-group-item-action">'.$nowbook->path.'</a>';
			}
		}
	}
	
?>