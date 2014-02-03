<?php
namespace AnhNhan\ModHub\Modules\Search\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Tag\TagQuery;
use AnhNhan\ModHub\Web\Application\JsonPayload;
use AnhNhan\ModHub\Web\Application\RawHttpPayload;


/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class AutocompleteTags extends Autocomplete
{
    public function process()
    {
        $request = $this->request;
        $accepts = $request->getAcceptableContentTypes();

        foreach ($accepts as $accept) {
            switch ($accept) {
                case 'application/json':
                case 'text/json':
                    return $this->handleJson();
                    break;
                case 'text/html':
                    return $this->handle();
                    break;
            }
        }

        return $this->handle();
    }

    public function handle()
    {
        return $this->handleJson();
    }

    public function handleJson()
    {
        $data = $this->retrieveData();
        $payload = new RawHttpPayload;
        $payload->setPayloadContents(json_encode((array) $data));
        $payload->setHttpHeader("Content-Type", "application/json");
        return $payload;
    }

    public function retrieveData()
    {
        $request = $this->request;
        $requestMethod = $request->getMethod();
        $inputQuery = $request->query->get("q");

        $tagApp = $this->app->getService("app.list")->app("tag");
        $query  = new TagQuery($tagApp->getEntityManager());

        $tagLabels = $query->searchTagLabelsStartingWith($inputQuery);
        return $tagLabels;
    }
}
