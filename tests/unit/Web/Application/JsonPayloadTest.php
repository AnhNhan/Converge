<?php
namespace Web\Application;

use AnhNhan\Converge;
use AnhNhan\Converge\Web\Application\JsonPayload;
use AnhNhan\Converge\Test\TestCase;

/**
 * This test also functions as a test for HttpPayload
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 *
 * @covers AnhNhan\Converge\Web\Application\JsonPayload
 * @covers AnhNhan\Converge\Web\Application\HttpPayload
 * @covers AnhNhan\Converge\Web\Application\AbstractPayload
 */
class JsonPayloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providePayloadContents
     */
    public function testPayloadContents($expContent, $content)
    {
        $payload = new JsonPayload($content);
        $payload->sendHttpHeaders(false);
        $expContent = preg_quote($expContent);
        self::assertRegExp('/\\n\\n\\{"payloads":'.$expContent.',"status":"ok"\\}$/', $payload->render());
    }

    public function providePayloadContents()
    {
        // Not representative
        return array(
            array('null', null),
            array('"foo"', 'foo'),
            array('{"foo":"bar"}', array("foo" => "bar")),
        );
    }

    public function testHasRightContentType()
    {
        $payload = new JsonPayload;
        $payload->sendHttpHeaders(false);
        self::assertRegExp("/Content-Type: application\\/json/", $payload->render());
    }

    public function testCanSetStatus()
    {
        $payload = new JsonPayload;
        $payload->sendHttpHeaders(false);
        $payload->setStatus("error");
        self::assertRegExp('/\\n\\n\\{"payloads":null,"status":"error"\\}$/', $payload->render());
    }
}
