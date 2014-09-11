<?php
namespace AnhNhan\Converge\Modules\Tag\Controllers;

use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TagDisplayController extends AbstractTagController
{
    public function handle()
    {
        $request = $this->request();
        $currentId = $request->request->get('id');
        $payload = $this->payload_html;
        $container = new MarkupContainer;
        $tag = create_tag_query($this->app()->getEntityManager())
            ->retrieveTag('TTAG-' . $currentId)
        ;

        if ($tag) {
            $container->push(h1($tag->label()));

            $container->push(h2($tag->description()));
        } else {
            $container->push(h1('Could not find a tag for \'' . $currentId . '\''));
        }

        $payload->setPayloadContents($container);
        return $payload;
    }
}
