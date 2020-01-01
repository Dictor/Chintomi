<?php
namespace Dictor\Chintomi;
require_once 'vendor/autoload.php';
require_once 'autoload.php';
use \PHPUnit\Framework\TestCase;

final class test_mdl_book extends TestCase {
    const TEST_PATH_JSON = 'tests/test_json';
    const TEST_PATH_SQLITE = 'tests/test_sqlite.db';
    private static $testHandlerSet = ['JSON', 'SQLITE'];
    
    public function testOpenDB(): void {
        mdl_book::SetDB('JSON');
        $this->assertEquals(0, mdl_book::UseDB(self::TEST_PATH_JSON));
        
        mdl_book::SetDB('SQLITE');
        $this->assertEquals(0, mdl_book::UseDB(self::TEST_PATH_SQLITE));
    }
    
    public function testAddBook(): void {
        $test_set = [
                new Comicbook(null, 'path1', 'name1', 'author1', 1, 1, 'date1'),
                new Comicbook(null, 'path2', 'name2', 'author2', 1, 1, 'date2'),
                new Comicbook(null, 'path3', 'name3', 'author3', 1, 1, 'date3'),
                new Comicbook(null, 'path4', 'name4', 'author4', 1, 1, 'date4')
            ];
        
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_book::SetDB($nowhandler);
            foreach($test_set as $now_set) {
                mdl_book::AddBook($now_set);
            }
            $this->assertCount(count($test_set), mdl_book::GetAllBooks());
        }
    }
    
    public function testSearchBook(): void {
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_book::SetDB($nowhandler);
            $all_book = mdl_book::GetAllBooks();
            $nowbook = mdl_book::SearchBook($all_book[0]->id);
            $this->assertCount(1, $nowbook);
            $this->assertEquals($all_book[0]->id, $nowbook[0]->id);
        }
    }
    
    public function testDeleteByPath(): void {
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_book::SetDB($nowhandler);
            mdl_book::DeleteBookByPath('path3');
            mdl_book::DeleteBookByPath('incorrect_path');
            $this->assertCount(3, mdl_book::GetAllBooks());
        }
    }
    
    /* DeleteByBook function isn't used (maybe?)
    
    public function testDeleteByBook(): void {
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_book::SetDB($nowhandler);
            mdl_book::DeleteBook(new Comicbook(1, null, null, null, null, null, null));
            mdl_book::DeleteBook(new Comicbook(10, null, null, null, null, null, null));
            $this->assertCount(2, mdl_book::GetAllBooks());
        }
    }
    */
    
    public function testDeleteAllBook(): void {
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_book::SetDB($nowhandler);
            mdl_book::DeleteAllBooks();
            $this->assertCount(0, mdl_book::GetAllBooks());
        }
    }
}
?>