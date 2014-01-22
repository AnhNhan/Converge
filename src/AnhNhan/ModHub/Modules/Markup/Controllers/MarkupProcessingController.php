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
        $requestMethod = $request->getMethod();
        $inputText = $request->request->get("text");

        if (!$inputText) {
            throw new \Exception("Input 'text' can't be empty!");
        }

        $stopWatch = $this->app()->getService("stopwatch");
        $timer = $stopWatch->start("markup-processing");

        $engine = new MarkupEngine();
        $engine->addInputText($inputText);
        $engine->process();
        $output = $engine->getOutputText();

        $time = $timer->stop()->getDuration();

        $payload = new JsonPayload();
        $payload->setPayloadContents(array(
            "contents" => (string) $output,
            "time"     => $time,
        ));
        return $payload;
    }
}
