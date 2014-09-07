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
        $draft_object_key = $request->get("draft_object_key");

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

        $last_saved_time = null;
        if ($draft_object_key && $user = $this->user)
        {
            $result = $this->internalSubRequest(
                urisprintf("draft/%s/%s", $user->uid, $draft_object_key),
                ["contents" => $inputText],
                "POST"
            );
            $last_saved_time = idx((array) idx((array) json_decode($result->getContent()), "payloads", []), "modified_at");
        }

        $time = $timer->stop()->getDuration();

        $payload = new JsonPayload();
        $payload->setPayloadContents(array(
            "contents" => (string) $output,
            "time"     => $time,
            "last_saved" => $last_saved_time ? "Last saved " . date("D d M h:m", $last_saved_time) : "Not saved",
        ));
        return $payload;
    }
}
