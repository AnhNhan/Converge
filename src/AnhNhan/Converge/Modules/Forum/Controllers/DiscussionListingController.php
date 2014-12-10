<?php
namespace AnhNhan\Converge\Modules\Forum\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumListing;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use AnhNhan\Converge\Web\Application\JsonPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use League\Fractal;
use AnhNhan\Converge\Modules\Forum\Transform\DiscussionTransformer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionListingController extends AbstractForumController
{
    private $discussionsPerPage = 20;

    public function process()
    {
        $request = $this->request();
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

    private function fetchDiscussions($limit, $offset, $query = null)
    {
        $query = $query ?: $this->buildQuery();
        $disqs = $query->retrieveDiscussions($limit, $offset);
        $query->fetchExternalsForDiscussions($disqs);
        return $disqs;
    }

    public function handle()
    {
        $request = $this->request();

        $pageNr = 0;

        if (($r_pageNr = $request->get("page-nr")) && preg_match("/^\\d+$/", $r_pageNr)) {
            $pageNr = $r_pageNr;
        }

        $offset = $pageNr * $this->discussionsPerPage;

        $query = $this->buildQuery();
        $disqs = $this->fetchDiscussions($this->discussionsPerPage, $offset, $query);
        // TODO: Have this cached in Discussion
        $postCounts = $query->fetchPostCountsForDiscussions($disqs);

        $container = new MarkupContainer;

        $container->push(h1('Forum Listing'));
        $listing = render_disq_listing($disqs, $postCounts);
        $container->push($listing);
        $container->push(a('Next page', 'disq/?page-nr=' . ($pageNr+1))->addClass('btn btn-large btn-primary'));
        $container->push(Converge\safeHtml('<style>.objects-list-container.forum-list-container{margin-top: 0;}</style>'));

        $payload = new HtmlPayload($container);
        return $payload;
    }

    public function handleJson()
    {
        $stopWatch = $this->app()->getService("stopwatch");
        $timer = $stopWatch->start("discussion-listing-json");

        $request = $this->request();
        $pageNr = 0;
        if (($r_pageNr = $request->get("page-nr")) && preg_match("/^\\d+$/", $r_pageNr)) {
            $pageNr = $r_pageNr;
        }
        $offset = $pageNr * $this->discussionsPerPage;
        $disqs = $this->fetchDiscussions($this->discussionsPerPage, $offset);

        $tags = ikey(array_mergev(pull($disqs, function ($disq)
        {
            return mpull(mpull($disq->tags->toArray(), 'tag'), 'toDictionary');
        })), 'uid');

        $fractal = new Fractal\Manager;
        $resource = new Fractal\Resource\Collection($disqs, new DiscussionTransformer($tags));

        $result = $fractal->createData($resource)->toArray();

        $time = $timer->stop()->getDuration();

        $payload = new JsonPayload();
        $payload->setPayloadContents(array(
            "discussions" => $result["data"], // TODO: Remove this once we've refactored payloads
            "time" => $time,
        ));
        return $payload;
    }
}
