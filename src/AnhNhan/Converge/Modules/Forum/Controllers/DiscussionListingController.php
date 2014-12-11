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
    private $acceptableSorts = [
        'createdAt' => true,
        'createdAtInverted' => true,
        'lastActivity' => true,
        'lastActivityInverted' => true,
    ];

    private $settings = [
        'page-size' => 20,
        'sorting'   => 'createdAt',
    ];

    private $pageNr = 0;

    public function process()
    {
        $request = $this->request();
        $accepts = $request->getAcceptableContentTypes();

        // Common request data gathering
        if (($r_pageNr = $request->get('page-nr')) && preg_match('/^\\d+$/', $r_pageNr)) {
            $this->pageNr = $r_pageNr;
        }

        if (($_sorting = $request->get('sorting')) && isset($this->acceptableSorts[$_sorting]))
        {
            $this->settings['sorting'] = $_sorting;
        }

        if (($_pageSize = $request->get('page-size')) && preg_match('/^\\d+$/', $_pageSize))
        {
            $this->settings['page-size'] = min(50, $_pageSize);
        }

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
        $offset = $this->pageNr * $this->settings['page-size'];

        $query = $this->buildQuery();
        $disqs = $this->fetchDiscussions($this->settings['page-size'], $offset, $query);
        // TODO: Have this cached in Discussion
        $postCounts = $query->fetchPostCountsForDiscussions($disqs);

        $container = new MarkupContainer;

        $container->push(h1('Forum Listing'));
        $listing = render_disq_listing($disqs, $postCounts);
        $container->push($listing);
        $container->push(a('Next page', 'disq/?page-nr=' . ($this->pageNr+1))->addClass('btn btn-large btn-primary'));
        $container->push(Converge\safeHtml('<style>.objects-list-container.forum-list-container{margin-top: 0;}</style>'));

        $payload = new HtmlPayload($container);
        return $payload;
    }

    public function handleJson()
    {
        $stopWatch = $this->app()->getService("stopwatch");
        $timer = $stopWatch->start("discussion-listing-json");

        $offset = $this->pageNr * $this->settings['page-size'];
        $disqs = $this->fetchDiscussions($this->settings['page-size'], $offset);

        $tags = ikey(array_mergev(pull($disqs, function ($disq)
        {
            return mpull(mpull($disq->tags->toArray(), 'tag'), 'toDictionary');
        })), 'uid');

        $fractal = new Fractal\Manager;
        $resource = new Fractal\Resource\Collection($disqs, new DiscussionTransformer($tags));

        $result = $fractal->createData($resource)->toArray();

        $time = $timer->stop()->getDuration();

        $lastEntry = last($disqs);

        $payload = new JsonPayload();
        $payload->setPayloadContents(array(
            "discussions" => $result["data"], // TODO: Remove this once we've refactored payloads
            "page-nr" => $this->pageNr,
            "settings" => $this->settings,
            "next-page-key" => ["time" => $lastEntry->createdAt->getTimestamp(), "uid" => $lastEntry->uid, "id" => $lastEntry->id],
            "has-next-page" => count($result["data"]) == $this->settings["page-size"],
            "time" => $time,
        ));
        return $payload;
    }
}
