<?php
namespace AnhNhan\Converge\Modules\Tag\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Tag\TagQuery;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use AnhNhan\Converge\Web\Application\JsonPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TagListingController extends AbstractTagController
{
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
        $container = new MarkupContainer;

        $tags = id(new TagQuery($this->app()))
            ->retrieveTags()
        ;

        $container->push(Converge\ht("h1", "Tags"));

        foreach ($tags as $tag) {
            $container->push(new TagView($tag->label(), $tag->color()));
        }

        // Add link to create new tag
        $container->unshift(Converge\ht("a", "Create new tag!", array(
            "href"  => "/tag/create",
            "class" => "btn btn-primary",
            "style" => "float: right;",
        )));

        $payload = new HtmlPayload($container);
        return $payload;
    }

    public function handleJson()
    {
        $result = array();

        $stopWatch = $this->app()->getService("stopwatch");
        $timer = $stopWatch->start("tag-listing-json");

        $tags = id(new TagQuery($this->app()))
            ->retrieveTags()
        ;

        foreach ($tags as $tag) {
            $result[] = $tag->toDictionary();
        }

        $time = $timer->stop()->getDuration();

        $payload = new JsonPayload();
        $payload->setPayloadContents(array(
            "tags" => $result,
            "time" => $time,
        ));
        return $payload;
    }
}
