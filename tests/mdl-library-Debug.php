<?php
namespace Dictor\Chintomi;
require_once 'vendor/autoload.php';
require_once 'autoload.php';
use \PHPUnit\Framework\TestCase;

final class test_mdl_library extends TestCase {
    public function testNewExploreDirectory(): void {
        printf("\n\nResult==\n");
        var_dump(mdl_library::NewExploreDirectory());
        printf("\n==\n");
    }
}