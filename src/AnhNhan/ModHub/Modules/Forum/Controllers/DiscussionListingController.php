<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumListing;
use AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\ModHub\Modules\Tag\Views\TagView;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionListingController extends AbstractForumController
{
    private $discussionsPerPage = 20;

    public function handle()
    {
        $request = $this->request();
        $app = $this->app();

        $forumEntityManager = $app->getEntityManager();
        $disqRepo = $forumEntityManager->getRepository("AnhNhan\\ModHub\\Modules\\Forum\\Storage\\Discussion");

        $pageNr = 0;
        $offset = $pageNr * $this->discussionsPerPage;

        $disqs = $disqRepo->findBy(array(), array("lastActivity" => "DESC"), $this->discussionsPerPage, $offset);

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
            "class" => "btn primary",
            "style" => "float: right;",
        )));

        $payload = new HtmlPayload($container);
        return $payload;
    }
}
