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
    
    public function testAddUser(): void {
        $test_set = [
            ['name1', 'password1', 1], 
            ['name2', 'password2', 300],
            ['name3', 'password3', 600],
            ['name4', 'password4', 998]
        ];
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_user::SetDB($nowhandler);
            foreach ($test_set as $now_set) {
                mdl_user::MakeUser($now_set[0], $now_set[1], $now_set[2]);
            }
            $this->assertCount(count($test_set), mdl_user::GetAllUser());
        }
    }
    
    public function testPassword(): void {
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_user::SetDB($nowhandler);
            $this->assertTrue(mdl_user::CheckPassword('name1', 'password1'));
            $this->assertFalse(mdl_user::CheckPassword('name1', 'notpassword1'));
            mdl_user::ChangePassword('name1', 'newpassword1');
            $this->assertTrue(mdl_user::CheckPassword('name1', 'newpassword1'));
            $this->assertFalse(mdl_user::CheckPassword('name1', 'password1'));
        }
    }
    
    public function testPermission(): void {
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_user::SetDB($nowhandler);
            $this->assertEquals(300, mdl_user::GetPermission('name2'));
            mdl_user::ChangePermission('name2', 400);
            $this->assertEquals(400, mdl_user::GetPermission('name2'));
        }
    }
    
    public function testAdmin(): void {
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_user::SetDB($nowhandler);
            $this->assertFalse(mdl_user::CheckAdminExist());
            mdl_user::MakeAdmin('name5', 'password5');
            $this->assertTrue(mdl_user::CheckAdminExist());
        }
    }
    
    public function testDeleteUser(): void {
        foreach (self::$testHandlerSet as $nowhandler) {
            mdl_user::SetDB($nowhandler);
            foreach (mdl_user::GetAllUser() as $uname => $uper) {
                mdl_user::DeleteUser($uname);
            }
            $this->assertCount(0, mdl_user::GetAllUser());
        }
    }
}