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
    public function handle()
    {
        $request = $this->request();
        $app = $this->app();

        $forumEntityManager = $app->getEntityManager();
        $disqRepo = $forumEntityManager->getRepository("AnhNhan\\ModHub\\Modules\\Forum\\Storage\\Discussion");
        $disqs = $disqRepo->findAll();

        $container = new MarkupContainer();

        $listing = new ForumListing;
        $listing->setTitle("Forum Listing");

        foreach ($disqs as $discussion) {
            $object = new ForumObject;
            $object
                ->setHeadline($discussion->label())
                ->setHeadHref("/disq/" . preg_replace("/^(.*?-)/", "", $discussion->uid()) . "/")
                ->addAttribute(ModHub\icon_text($discussion->posts()->count(), "th-list", false));

            $tags = mpull($discussion->tags()->toArray(), "tagId");
            foreach ($tags as $tagLabel) {
                if (!empty($tagLabel)) {
                    $object->addTag(new TagView(substr(preg_replace("/^(.*?-)/", "", $tagLabel), 0, rand(3, 8))));
                }
            }

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
