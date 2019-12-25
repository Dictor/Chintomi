<?php
	namespace Dictor\Chintomi;
	
    interface handler {
		public static function Open($path): int;
		public static function Close(): void;
		public static function Execute(string $preQuery, array $parameter);
		public static function Query(string $preQuery, array $parameter);
		public static function ResultToArray($res): array;
		public static function ResultToComicbook($res): array;
	}
?>