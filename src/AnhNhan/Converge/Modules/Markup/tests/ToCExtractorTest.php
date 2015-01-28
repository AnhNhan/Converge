<?php
namespace AnhNhan\Converge\Modules\Markup\Test;

use AnhNhan\Converge\Modules\Markup\TOCExtractor;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class ToCExtractorTest extends \PHPUnit_Framework_TestCase
{
    private function convertTestOutput(array $array)
    {
        $r = array_map(function ($in) {
            return sprintf("%d:%s %s", $in["level"], $in["type"], $in["text"]);
        }, $array);
        return implode("\n", $r);
    }

    public function testConvertTestOutput()
    {
        self::assertEquals(
            "1:h2 A test heading",
            $this->convertTestOutput(array(
                array(
                    "level" => 1,
                    "type"  => "h2",
                    "text"  => "A test heading",
                )
            ))
        );
    }

    /**
     * @dataProvider provideFixtures
     */
    public function test_($markdown, $expected)
    {
        $result = id(new TOCExtractor)->parseAndExtract($markdown);
        self::assertEquals($expected, $this->convertTestOutput($result));
    }

    public function provideFixtures()
    {
        $separator = "\n@@---------------------------@@\n";
        $path = __DIR__ . '/fixtures/';

        $r = array();

        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            list($markup, $expected) = explode($separator, str_replace("\r", "", file_get_contents($fileInfo->getPathname())));
            $r[] = array(trim($markup), trim($expected));
        }

        return $r;
    }
}
