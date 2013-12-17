<?php
namespace AnhNhan\ModHub\Test\Storage\Types;

use AnhNhan\ModHub\Storage\Types\UID;
use AnhNhan\ModHub\Test\TestCase;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class UIDTest extends TestCase
{
    /**
     * @dataProvider provideValidUIDs
     */
    public function testCanCheckValidity($uidString)
    {
        self::assertTrue(UID::checkValidity($uidString));
    }

    /**
     * @dataProvider provideValidUIDs
     * @testdox Can split unique IDs
     */
    public function testCanSplitUIDs($uidString, $name, $id)
    {
        $uid = new UID($uidString);
        self::assertEquals($name, $uid->getName());
        self::assertEquals($id, $uid->getId());
    }

    public function provideValidUIDs()
    {
        return array(
            array("DERP-sdie9v7b", "DERP", "sdie9v7b"),
            array("XXXX-sp3n2mdo", "XXXX", "sp3n2mdo"),
        );
    }
}
