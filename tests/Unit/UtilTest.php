<?php


namespace Tests\Unit;

use Gaffer\Util;
use Tests\Support\UnitTester;

class UtilTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testTrimInternal()
    {
        $this->assertEquals("123 123", Util::trimInternal("123       123"));
        $this->assertEquals("123 123", Util::trimInternal("     123       123"));
        $this->assertEquals("123 123", Util::trimInternal("     123       123     "));
    }

    public function testCamelCaseToSnakeCase()
    {

    }
}
