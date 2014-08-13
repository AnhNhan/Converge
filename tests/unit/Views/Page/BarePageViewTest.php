<?php
namespace Views\Page;

use AnhNhan\Converge\Views\Page\BarePageView;
use AnhNhan\Converge\Test\TestCase;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class BarePageViewTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyPage()
    {
        $page = new BarePageView("");
        self::assertEquals("", (string) $page);
    }

    public function testSimplePage()
    {
        $page = new BarePageView("<h1>Hello World!</h1>");
        self::assertEquals("<h1>Hello World!</h1>", (string) $page);
    }

    public function testHasNoTitle()
    {
        $page = new BarePageView("foo");
        self::assertEquals("", $page->getTitle());
    }

    public function testCanGetContents()
    {
        $page = new BarePageView("foo");
        self::assertEquals("foo", $page->getContent());
    }
}
