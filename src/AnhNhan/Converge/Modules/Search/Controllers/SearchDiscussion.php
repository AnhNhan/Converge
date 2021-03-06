<?php
namespace AnhNhan\Converge\Modules\Search\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\Converge\Web\Application\JsonPayload;

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
        $limit = $request->query->get('limit', 20);

        assert(is_array($tagIdInc));
        assert(is_array($tagIdExc));

        $forumApp = $this->externalApp('forum');
        $query  = new DiscussionQuery($forumApp->getEntityManager());

        $disqs = $query->retrieveDiscussionsSearchTags($tagIdInc, $tagIdExc, $limit);
        $result_set = mpull($disqs, 'uid');
        return $result_set;
    }
}
