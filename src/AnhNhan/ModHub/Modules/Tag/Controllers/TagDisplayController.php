<?php
namespace AnhNhan\ModHub\Modules\Tag\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Tag\TagQuery;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TagDisplayController extends AbstractTagController
{
    public function handle()
    {
        $request = $this->request();
        $currentId = $request->request->get("id");
        $payload = new HtmlPayload;
        $container = new MarkupContainer;
        $tag = id(new TagQuery($this->app()->getEntityManager()))
            ->retrieveTag("TTAG-" . $currentId)
        ;

        if ($tag) {
            $container->push(ModHub\ht("h1", $tag->label()));

            $container->push(ModHub\ht("h2", $tag->description()));
        } else {
            $container->push(ModHub\ht("h1", "Could not find a tag for '" . $currentId . "'"));
        }

        $payload->setPayloadContents($container);
        return $payload;
    }
}
