<?php
namespace AnhNhan\Converge\Modules\Tag\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Tag\TagQuery;
use AnhNhan\Converge\Web\Application\HtmlPayload;
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
            $container->push(Converge\ht("h1", $tag->label()));

            $container->push(Converge\ht("h2", $tag->description()));
        } else {
            $container->push(Converge\ht("h1", "Could not find a tag for '" . $currentId . "'"));
        }

        $payload->setPayloadContents($container);
        return $payload;
    }
}
