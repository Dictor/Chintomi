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
                new Comicbook(null, 'path1', 'name1', 'author1', 2, 3, 'date1'),
                new Comicbook(null, 'path2', 'name2', 'author2', 1, 4, 'date2'),
                new Comicbook(null, 'path3', 'name3', 'author3', 4, 1, 'date3'),
                new Comicbook(null, 'path4', 'name4', 'author4', 3, 2, 'date4')
            ];
        
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_book::SetDB($nowhandler);
            foreach($test_set as $now_set) {
                mdl_book::AddBook($now_set);
                usleep(250000);
            }
            $this->assertCount(count($test_set), mdl_book::GetAllBooks());
            $this->assertCount(count($test_set), mdl_book::GetBooks('', new com_sort_dropdown('nameu')));
        }
    }
    
    public function testGetBooks(): void {
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_book::SetDB($nowhandler);
            $res = mdl_book::GetBooks('name3', new com_sort_dropdown('nameu'));
            $this->assertCount(1, $res);
            $this->assertEquals($res[0]->name, "name3");
        }
    }
    
    public function testGetBooksSort(): void {
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_book::SetDB($nowhandler);
            $res = mdl_book::GetBooks('', new com_sort_dropdown('named'));
            $this->assertEquals($res[0]->name, "name4");
            $this->assertEquals($res[3]->name, "name1");
            
            $res = mdl_book::GetBooks('', new com_sort_dropdown('sizeu'));
            $this->assertEquals($res[0]->name, "name3");
            $this->assertEquals($res[3]->name, "name2");
            
            $res = mdl_book::GetBooks('', new com_sort_dropdown('paged'));
            $this->assertEquals($res[0]->name, "name3");
            $this->assertEquals($res[3]->name, "name2");
            
            $res = mdl_book::GetBooks('', new com_sort_dropdown('dateu'));
            $this->assertTrue((new \DateTime($res[0]->added_date)) <= (new \DateTime($res[3]->added_date)));
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
    
    public function testDeleteAllBook(): void {
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_book::SetDB($nowhandler);
            mdl_book::DeleteAllBooks();
            $this->assertCount(0, mdl_book::GetAllBooks());
        }
    }
}
?>