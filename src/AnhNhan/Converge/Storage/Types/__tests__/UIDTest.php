<?php
namespace AnhNhan\Converge\Storage\Types;

use AnhNhan\Converge\Storage\Types\UID;
use AnhNhan\Converge\Test\TestCase;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class UIDTest extends \PHPUnit_Framework_TestCase
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
    public function testCanSplitUIDs($uidString, $type, $id)
    {
        $uid = new UID($uidString);
        self::assertEquals($type, $uid->getType());
        self::assertEquals($id, $uid->getId());
    }

    /**
     * @dataProvider provideValidUIDs
     * @testdox __toString gives back the whole UID string
     */
    public function testToString($uidString)
    {
        $uid = new UID($uidString);
        self::assertEquals($uidString, (string) $uid);
    }

    public function provideValidUIDs()
    {
        return array(
            array("DERP-sdif36zeer9v7bg8", "DERP", "sdif36zeer9v7bg8"),
            array("XXXX-sp3nhgdr2mdseolk", "XXXX", "sp3nhgdr2mdseolk"),
        );
    }

    /**
     * @dataProvider provideInvalidUIDs
     * @testdox Recognizes invalid UIDs
     */
    public function testRecognizesInvalidUIDs($uidString, $type, $id, $message)
    {
        self::assertFalse(UID::checkValidity($uidString), $message);
    }

    /**
     * @dataProvider provideInvalidUIDs
     * @testdox Can't construct with invalid UIDs
     * @expectedException \InvalidArgumentException
     */
    public function testCantConstructInvalidUIDs($uidString, $type, $id, $message)
    {
        new UID($uidString);
    }

    public function provideInvalidUIDs()
    {
        return array(
            array("DERPX-sdif36zeer9v7bg8", "DERPX", "sdif36zeer9v7bg8", "Type should only be 4 characters"),
            array("XXXX-sp3nhgdrdr2mdolk2", "XXXX", "sp3nhgdrdr2mdolk2", "Random id part should only be 14 characters"),
        );
    }

    /**
     * @dataProvider provideInvalidLengths
     * @testdox Cant' generate UIDs with invalid lengths
     * @expectedException \InvalidArgumentException
     */
    public function testCantGenerateInvalidUIDLengths($invalidLength)
    {
        UID::generate(UID::TYPE_DEFAULT, $invalidLength);
    }

    public function provideInvalidLengths()
    {
        return array(array(0), array(13), array(15), array(20), array(21), array(23), array(PHP_INT_MAX));
    }

    /**
     * @testdox Can generate valid UIDs
     */
    public function testCanGenerateValidUIDs()
    {
        $uid = UID::generate();
        self::assertTrue(UID::checkValidity($uid), "The generated string '$uid' should be a valid UID");

        $uid = UID::generate("XXXX");
        self::assertTrue(UID::checkValidity($uid), "The generated string '$uid' should be a valid UID");
        $uid = UID::generate("XXXX-YYYY");
        self::assertTrue(UID::checkValidity($uid), "The generated string '$uid' should be a valid UID");
    }

    /**
     * @testdox Can generate UID instances
     */
    public function testCanGenerateUIDInstances()
    {
        $uid = UID::generateNew();
        self::assertInstanceOf('AnhNhan\Converge\Storage\Types\UID', $uid);

        $uid = UID::generateNew("XXXX");
        self::assertInstanceOf('AnhNhan\Converge\Storage\Types\UID', $uid);
        $uid = UID::generateNew("XXXX-YYYY");
        self::assertInstanceOf('AnhNhan\Converge\Storage\Types\UID', $uid);
    }

    /**
     * @dataProvider provideInvalidTypes
     * @testdox Can't generate with invalid types
     * @expectedException \InvalidArgumentException
     */
    public function testCantGenerateWithInvalidTypes($invalidType, $message)
    {
        UID::generate($invalidType, $message);
    }

    public function provideInvalidTypes()
    {
        return array(
            array("ABCDE", "Type has to be four characters long"),
            array("ABC", "Type has to be four characters long"),
            array("1234", "Type has to be alphanumeric"),
            array("abcd", "Type has to be uppercase"),
            array("abcd-efgh", "Type has to be uppercase"),
            array("ABCD-efgh", "Type has to be uppercase"),
            array("ABCDE-EFGH", "Type has to be four characters long"),
            array("ABCD-EFGHI", "Subtype has to be four characters long"),
            array("ABCDEFGH", "Type and subtype have to be separated by a dash"),
        );
    }
}
