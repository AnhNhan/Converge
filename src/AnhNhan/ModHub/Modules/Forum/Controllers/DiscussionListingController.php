<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Query\DiscussionQuery;
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

    public function handle()
    {
        $request = $this->request();

        $pageNr = 0;
        $offset = $pageNr * $this->discussionsPerPage;

        $disqs = id(new DiscussionQuery($this->app()))
            ->retrieveDiscussions($this->discussionsPerPage, $offset)
        ;

        $container = new MarkupContainer();

        $listing = new ForumListing;
        $listing->setTitle("Forum Listing");

        foreach ($disqs as $discussion) {
            $object = new ForumObject;
            $object
                ->setHeadline($discussion->label())
                ->setHeadHref("/disq/" . preg_replace("/^(.*?-)/", "", $discussion->uid()))
                ->postCount($discussion->posts()->count());

            $tags = mpull(mpull($discussion->tags()->toArray(), "tag"), "label");
            foreach ($tags as $tagLabel) {
                if (!empty($tagLabel)) {
                    $object->addTag(new TagView($tagLabel));
                }
            }

            $object->addAttribute($discussion->authorId());
            $object->addAttribute($discussion->lastActivity()->format("D, d M 'y"));

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
        $disqs = id(new DiscussionQuery($this->app()))
            ->retrieveDiscussions($this->discussionsPerPage, $offset)
        ;

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
