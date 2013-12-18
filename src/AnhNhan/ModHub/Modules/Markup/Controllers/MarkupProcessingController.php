<?php
namespace AnhNhan\ModHub\Modules\Markup\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Markup\MarkupEngine;
use AnhNhan\ModHub\Web\Application\JsonPayload;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class MarkupProcessingController extends AbstractMarkupController
{
    public function handle()
    {
        $request = $this->request();
        $request->populateFromServer(array("REQUEST_METHOD"));
        $request->populateFromRequest(array("text"));

        $requestMethod = $request->getServerValue("request_method");
        $inputText = $request->getRequestValue("text");

        if (!$inputText) {
            throw new \Exception("Input 'text' can't be empty!");
        }

        $engine = new MarkupEngine();
        $engine->addInputText($inputText);
        $engine->process();
        $output = $engine->getOutputText();

        $payload = new JsonPayload();
        $payload->setPayloadContents($output);
        return $payload;
    }
}
