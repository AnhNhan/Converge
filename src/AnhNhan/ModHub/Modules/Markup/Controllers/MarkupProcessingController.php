<?php
namespace AnhNhan\ModHub\Modules\Markup\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Markup\MarkupEngine;
use AnhNhan\ModHub\Web\Application\JsonPayload;
use Symfony\Component\Stopwatch\Stopwatch;

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

        $stopWatch = new Stopwatch;
        $timer = $stopWatch->start("markup-processing");

        $engine = new MarkupEngine();
        $engine->addInputText($inputText);
        $engine->process();
        $output = $engine->getOutputText();

        $time = $timer->stop()->getDuration();

        $payload = new JsonPayload();
        $payload->setPayloadContents(array(
            "contents" => $output,
            "time"     => $time,
        ));
        return $payload;
    }
}
