<?php
namespace AnhNhan\ModHub\Test\Web\Application;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Web\Application\RawHttpPayload;
use AnhNhan\ModHub\Test\TestCase;

/**
 * This test also functions as a test for HttpPayload
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 *
 * @covers AnhNhan\ModHub\Web\Application\RawHttpPayload
 * @covers AnhNhan\ModHub\Web\Application\HttpPayload
 * @covers AnhNhan\ModHub\Web\Application\AbstractPayload
 */
class RawHttpPayloadTest extends TestCase
{
    public function testSimplePayload()
    {
        $contents = "derp";
        $payload = new RawHttpPayload($contents);
        $payload->sendHttpHeaders(false);
        self::assertRegExp("/\\n\\nderp$/", $payload->render(), "The contents should be at the end of the rendered payload");
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

        self::assertRegExp("/\\nHerp-Head: Derp-Content\\n.*?\\n\\n.*?$/", $payload->render(), "The header should appear in the header block of the rendered payload");
    }
}
