<?php
namespace AnhNhan\Converge\Modules\Markup\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Markup\MarkupEngine;
use AnhNhan\Converge\Web\Application\JsonPayload;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class MarkupProcessingController extends AbstractMarkupController
{
    public function handle()
    {
        $request = $this->request();
        $requestMethod = $request->getMethod();
        $inputText = $request->get("text");

        if (!$inputText) {
            throw new \Exception("Input 'text' can't be empty!");
        }

        $stopWatch = $this->app()->getService("stopwatch");
        $timer = $stopWatch->start("markup-processing");

        $rules = get_custom_markup_rules($this->app->getService('app.list'));
        $engine = new MarkupEngine();
        $engine->setCustomRules($rules);
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
