<?php
namespace AnhNhan\ModHub\Modules\Examples\Examples;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumListing;
use AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\ModHub\Modules\Tag\Views\TagView;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ForumListingExample extends AbstractExample
{
    public function getName()
    {
        return "forum-listing";
    }

    public function getExample()
    {
        $container = new MarkupContainer;

        $listing = new ForumListing;
        $listing->setTitle('Forum Listing');

        $listing->addObject(
            id(new ForumObject)
                ->setHeadline('A little story of the future')
                ->addTag(new TagView("caw", "green"))
                ->addTag(new TagView("internal"))
        );
        $listing->addObject(
            id(new ForumObject)
                ->setHeadline('Why the future is the future')
                ->addTag(new TagView("caw", "green"))
                ->addTag(new TagView("sotp", "blue"))
                ->addTag(new TagView("homefront", "dark"))
                ->addTag(new TagView("discussion"))
        );
        $listing->addObject(
            id(new ForumObject)
                ->setHeadline('Future, I am your father')
                ->addTag(new TagView("caw", "green"))
                ->addTag(new TagView("sotp", "blue"))
        );
        $row = ModHub\ht("div")->addClass("row-flex");
        $row->appendContent($listing->render()->addClass("width12"))->appendContent($listing->render()->addClass("width6"))->appendContent($listing->render()->addClass("width6"))->appendContent($listing->render()->addClass("width12"));
        $container->push($row);

        return $container;
    }
}
