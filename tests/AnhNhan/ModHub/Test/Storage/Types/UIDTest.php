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
            array("DERP-sdif36ze9v7bg8", "DERP", "sdif36ze9v7bg8"),
            array("XXXX-sp3nhgdr2mdolk", "XXXX", "sp3nhgdr2mdolk"),
        );
    }

    /**
     * @dataProvider provideInvalidUIDs
     * @testdox Recognizes invalid UIDs
     */
    public function testRecognizesInvalidUIDs($uidString, $name, $id, $message)
    {
        self::assertFalse(UID::checkValidity($uidString), $message);
    }

    public function provideInvalidUIDs()
    {
        return array(
            array("DERPX-sdif36ze9v7bg8", "DERPX", "sdif36ze9v7bg8", "Name should only be 4 characters"),
            array("XXXX-sp3nhgdr2mdolk2", "XXXX", "sp3nhgdr2mdolk2", "Random id part should only be 14 characters"),
        );
    }

    public function testCanGenerateValidUIDs()
    {
        $uid = UID::generate();
        self::assertTrue(UID::checkValidity($uid), "The generated string '$uid' should be a valid UID");
    }
}
