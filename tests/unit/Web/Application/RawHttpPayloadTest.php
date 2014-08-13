<?php
namespace Web\Application;

use AnhNhan\Converge;
use AnhNhan\Converge\Web\Application\RawHttpPayload;
use AnhNhan\Converge\Test\TestCase;

/**
 * This test also functions as a test for HttpPayload
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 *
 * @covers AnhNhan\Converge\Web\Application\RawHttpPayload
 * @covers AnhNhan\Converge\Web\Application\HttpPayload
 * @covers AnhNhan\Converge\Web\Application\AbstractPayload
 */
class RawHttpPayloadTest extends \PHPUnit_Framework_TestCase
{
    public function testSimplePayload()
    {
        $contents = "derp";
        $payload = new RawHttpPayload($contents);
        $payload->sendHttpHeaders(false);
        self::assertRegExp("/\\n\\n{$contents}$/", $payload->render(), "The contents should be at the end of the rendered payload");
    }

    public function testCanSetHeaders()
    {
        $contents = "derp";
        $payload = new RawHttpPayload($contents);
        $payload->sendHttpHeaders(false);

        $payload->setHttpHeader("Herp-Head", "Derp-Content");

        $headers = $payload->getHttpHeaders();
        self::assertArrayHasKey("Herp-Head", $headers);
        self::assertEquals($headers["Herp-Head"], "Derp-Content");

        self::assertRegExp("/\\nHerp-Head: Derp-Content\\n.*?\\n\\n{$contents}$/", $payload->render(), "The header should appear in the header block of the rendered payload");
    }
}
