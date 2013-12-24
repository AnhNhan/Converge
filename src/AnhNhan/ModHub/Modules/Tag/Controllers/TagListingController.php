<?php
namespace AnhNhan\ModHub\Modules\Tag\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Tag\Views\TagView;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TagListingController extends AbstractTagController
{
    public function handle()
    {
        $request = $this->request();
        $app = $this->app();
        $container = new MarkupContainer;

        $tagEntityManager = $app->getEntityManager();
        $tagRepo = $tagEntityManager->getRepository("AnhNhan\\ModHub\\Modules\\Tag\\Storage\\Tag");
        $tags = $tagRepo->findAll();

        $container->push(ModHub\ht("h1", "Tags"));

        foreach ($tags as $tag) {
            $container->push(new TagView($tag->label(), $tag->color()));
        }

        // Add link to create new tag
        $container->unshift(ModHub\ht("a", "Create new tag!", array(
            "href"  => "/tag/create",
            "class" => "btn primary",
            "style" => "float: right;",
        )));

        $payload = new HtmlPayload($container);
        return $payload;
    }
}
