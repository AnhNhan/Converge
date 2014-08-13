<?php
namespace AnhNhan\Converge\Modules\Examples\Examples;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumListing;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\Converge\Modules\Forum\Views\Objects\PaneledForumListing;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
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
                ->addTag(new TagView("internal", "dark"))
                ->addTag(new TagView("caw"))
        );
        $listing->addObject(
            id(new ForumObject)
                ->setHeadline('Why the future is the future')
                ->addTag(new TagView("caw"))
                ->addTag(new TagView("homefront"))
                ->addTag(new TagView("sotp"))
                ->addTag(new TagView("discussion"))
        );
        $listing->addObject(
            id(new ForumObject)
                ->setHeadline('Future, I am your father')
                ->addTag(new TagView("caw"))
                ->addTag(new TagView("sotp"))
        );

        $renderedListing = $listing->render();

        $panelForumListing = id(new PaneledForumListing)
            ->setTitle(Converge\ht("h3", "Forum Listing... IN PANELS!"))
            ->addTag(new TagView("caw"))
            ->addTag(new TagView("sotp"))
            ->addObject(
                id(new ForumObject)
                    ->setHeadline('A little story of the future')
                    ->addTag(new TagView("caw"))
                    ->addTag(new TagView("sotp"))
            )
            ->addObject(
            id(new ForumObject)
                    ->setHeadline('Why the future is the future')
                    ->addTag(new TagView("internal", "dark"))
                    ->addTag(new TagView("caw"))
                    ->addTag(new TagView("homefront"))
                    ->addTag(new TagView("sotp"))
                    ->addTag(new TagView("discussion"))
            )
            ->addObject(
                id(new ForumObject)
                    ->setHeadline('Future, I am your father')
                    ->addTag(new TagView("caw"))
                    ->addTag(new TagView("sotp"))
            )
            ->render()
        ;

        $grid = new \AnhNhan\Converge\Views\Grid\Grid;
        $row = $grid->row()
            ->column(12)->push($renderedListing)
            ->parentRow()->parent()->row()
                ->column(6)->push($renderedListing)
                ->parentRow()
                ->column(6)->push($renderedListing)
            ->parentRow()->parent()->row()
            ->column(12)->push($renderedListing)

            ->parentRow()

            ->column(12)->push($panelForumListing)
            ->parentRow()->parent()->row()
                ->column(6)->push($panelForumListing)
                ->parentRow()
                ->column(6)->push($panelForumListing)
            ->parentRow()->parent()->row()
                ->column(4)->push($panelForumListing)
                ->parentRow()
                ->column(4)->push($panelForumListing)
                ->parentRow()
                ->column(4)->push($panelForumListing)
            ->parentRow()->parent()->row()
            ->column(12)->push($panelForumListing)
        ;
        $container->push($grid);

        return $container;
    }
}
