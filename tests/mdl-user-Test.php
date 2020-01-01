<?php
namespace Dictor\Chintomi;
require_once 'vendor/autoload.php';
require_once 'autoload.php';
use \PHPUnit\Framework\TestCase;

final class test_mdl_user extends TestCase {
    const TEST_PATH_JSON = 'tests/test_json';
    const TEST_PATH_SQLITE = 'tests/test_sqlite.db';
    private static $testHandlerSet = ['JSON', 'SQLITE'];
    
    public function testOpenDB(): void {
        mdl_user::SetDB('JSON');
        $this->assertEquals(0, mdl_user::UseDB(self::TEST_PATH_JSON));
        
        mdl_user::SetDB('SQLITE');
        $this->assertEquals(0, mdl_user::UseDB(self::TEST_PATH_SQLITE));
    }
}