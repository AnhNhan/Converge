<?php
namespace AnhNhan\Converge\Infrastructure\Test;

use AnhNhan\Converge\Infrastructure\MagicConstructor;
use AnhNhan\Converge\Infrastructure\MagicProperty;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class MagicConstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testT1_1()
    {
        $obj = new T1();
        self::assertTrue(true);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testT1_2()
    {
        $obj = new T1(1);
        self::fail('Did not emit exception.');
    }

    public function testT2_1()
    {
        $obj = new T2(1, 2);
        self::assertEquals(1, $obj->val1);
        self::assertEquals(2, $obj->val2);
    }

    /**
     * @expectedException \Exception
     * @dataProvider provideExceptionCases
     */
    public function testT2_Exceptions($lazy_fun)
    {
        $lazy_fun();
        self::fail('Did not emit exception.');
    }

    public function provideExceptionCases()
    {
        return [
            [function () { new T2(); }],
            [function () { new T2(1); }],
            [function () { new T2(1, 2, 3); }],
        ];
    }

    public function testT3()
    {
        $obj = new T3(4, 5);
        self::assertEquals(4, $obj->val1);
        self::assertEquals(5, $obj->val2);
    }
}

class T1
{
    use MagicConstructor;
}

class T2
{
    use MagicConstructor;

    public $val1;
    public $val2;
}

class T3
{
    use MagicConstructor;
    use MagicProperty;

    private $val1;
    private $val2;
}
