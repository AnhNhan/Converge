<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumListing;
use AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\ModHub\Modules\Tag\Views\TagView;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use AnhNhan\ModHub\Web\Application\JsonPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use League\Fractal;
use AnhNhan\ModHub\Modules\Forum\Transform\DiscussionTransformer;

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
        $disqs = $query->retrieveDiscussions($this->discussionsPerPage, $offset);
        $query->fetchExternalsForDiscussions($disqs);
        return $disqs;
    }

    public function handle()
    {
        $request = $this->request();

        $pageNr = 1;

        if ($request->request->has("page-nr") && ($r_pageNr = $request->request->get("page-nr")) && preg_match("/^\\d+$/", $r_pageNr)) {
            $pageNr = $r_pageNr;
        }

        --$pageNr;

        $offset = $pageNr * $this->discussionsPerPage;

        $query = $this->buildQuery();
        $disqs = $this->fetchDiscussions($this->discussionsPerPage, $offset, $query);
        // TODO: Have this cached in Discussion
        $postCounts = $query->fetchPostCountsForDiscussions($disqs);

        $container = new MarkupContainer;

        $listing = new ForumListing;
        $listing->setTitle("Forum Listing");

        foreach ($disqs as $discussion) {
            $object = new ForumObject;
            $object
                ->setHeadline($discussion->label)
                ->setHeadHref("/disq/" . $discussion->cleanId)
                ->postCount(idx($postCounts, $discussion->uid)["postcount"]);

            $tags = mpull($discussion->tags->toArray(), "tag");
            $tags = msort($tags, "label");
            $tags = array_reverse($tags);
            $tags = msort($tags, "displayOrder");
            foreach ($tags as $tag) {
                if (!empty($tag)) {
                    $object->addTag(new TagView($tag->label, $tag->color));
                }
            }

            $object->addDetail($discussion->lastActivity->format("D, d M 'y"));
            $object->addDetail($discussion->author->name);

            $listing->addObject($object);
        }

        $container->push($listing);

        // Add link to create new discussion
        $container->unshift(ModHub\ht("a", "Create new discussion!", array(
            "href"  => "/disq/create",
            "class" => "btn btn-primary",
            "style" => "float: right;",
        )));

        $payload = new HtmlPayload($container);
        return $payload;
    }

    public function handleJson()
    {
        $stopWatch = $this->app()->getService("stopwatch");
        $timer = $stopWatch->start("discussion-listing-json");

        $pageNr = 0;
        $offset = $pageNr * $this->discussionsPerPage;
        $disqs = $this->fetchDiscussions($this->discussionsPerPage, $offset);

        $fractal = new Fractal\Manager;
        $resource = new Fractal\Resource\Collection($disqs, new DiscussionTransformer);

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
