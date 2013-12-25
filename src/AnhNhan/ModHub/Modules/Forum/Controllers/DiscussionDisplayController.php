<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionDisplayController extends AbstractForumController
{
    public function handle()
    {
        $request = $this->request();
        $app = $this->app();

        $currentId = $request->getValue("id");

        $forumEntityManager = $app->getEntityManager();
        $disqRepo = $forumEntityManager->getRepository("AnhNhan\\ModHub\\Modules\\Forum\\Storage\\Discussion");

        /**
         * @var \AnhNhan\ModHub\Modules\Forum\Storage\Discussion
         */
        $disq = $disqRepo->find("DISQ-" . $currentId);

        $payload = new HtmlPayload;
        $container = new MarkupContainer;

        if ($disq) {
            $container->push(ModHub\ht("h1", $disq->label()));

            $container->push(ModHub\ht("h2", $disq->text()));
            
            foreach ($disq->posts()->toArray() as $post) {
                $container->push(ModHub\ht("p", $post->rawText()));
            }
        } else {
            $container->push(ModHub\ht("h1", "Could not find a discussion for '" . $currentId . "'"));
        }

        $payload->setPayloadContents($container);
        return $payload;
    }
}
