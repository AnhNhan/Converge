<?php
namespace AnhNhan\ModHub\Modules\Search\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\ModHub\Web\Application\JsonPayload;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class SearchDiscussion extends Search
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
        $payload = new JsonPayload;
        $payload->setPayloadContents($data);
        $payload->setHttpHeader('Content-Type', 'application/json');
        return $payload;
    }

    public function retrieveData()
    {
        $request = $this->request;
        $requestMethod = $request->getMethod();

        $tagIdInc = $request->query->get('tid_inc', []);
        $tagIdExc = $request->query->get('tid_exc', []);

        assert(is_array($tagIdInc));
        assert(is_array($tagIdExc));

        $forumApp = $this->app->getService('app.list')->app('forum');
        $query  = new DiscussionQuery($forumApp->getEntityManager());

        $disqs = $query->retrieveDiscussionsSearchTags($tagIdInc, $tagIdExc);
        $result_set = mpull($disqs, 'uid');
        return $result_set;
    }
}
