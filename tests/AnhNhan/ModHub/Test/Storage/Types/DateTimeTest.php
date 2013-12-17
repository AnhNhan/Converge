<?php
namespace AnhNhan\ModHub\Test\Storage\Types;

use AnhNhan\ModHub\Storage\Types\DateTime;
use AnhNhan\ModHub\Test\TestCase;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class DateTimeTest extends TestCase
{
    /**
     * @dataProvider provideValidTimestamps
     */
    public function testCanRecognizeValidTimestamps($timestamp, $msg = null)
    {
        self::assertTrue(DateTime::checkValidity($timestamp), $msg);
    }

    public function provideValidTimestamps()
    {
        return array(
            array(123456789),
            array(time()),
            array(0),
            array(1),
            array("12345", "Timestamps embedded in strings should be valid"),
        );
    }

    /**
     * @dataProvider provideInvalidTimestamps
     */
    public function testCanRecognizeInvalidTimestamps($timestamp, $msg = null)
    {
        self::assertFalse(DateTime::checkValidity($timestamp), $msg);
    }

    public function provideInvalidTimestamps()
    {
        return array(
            array(-123, "Sadly we do not support negative timestamps"),
            array(123.45, "Sub-second precision is not the purpose of DateTime"),
            array("1st January"),
            array("19:00"),
            array("7pm"),
        );
    }
}
