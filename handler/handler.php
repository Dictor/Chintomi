<?php
namespace Dictor\Chintomi;
	
interface handler {
	public static function Open($path): int;
	public static function Close(): void;
	
	/*
	이 두개의 메서드가 핸들러 인터페이스 구현에 필수적일지는 검토 필요 (함수가 SQL text query에만 종속적임)
	public static function Execute(string $preQuery, array $parameter);
	public static function Query(string $preQuery, array $parameter);
	*/
	
	public static function ResultToArray($res): array;
	public static function ResultToComicbook($res): array;
}
?>