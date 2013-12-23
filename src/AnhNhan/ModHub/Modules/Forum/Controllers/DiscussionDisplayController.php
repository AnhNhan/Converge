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

            if ($disq->firstPost()) {
                $container->push(ModHub\ht("h2", $disq->firstPost()->rawText()));
            }

            $hadFirstPost = (bool)$disq->firstPost();
            $ii = 0;
            foreach ($disq->posts()->toArray() as $post) {
                if ($ii === 0 && $hadFirstPost) { // Skip the first post if had first post
                    $ii++;
                    continue;
                }

                $container->push(ModHub\ht("p", $post->rawText()));
                $ii++;
            }
        } else {
            $container->push(ModHub\ht("h1", "Could not find a discussion for '" . $currentId . "'"));
        }

        $payload->setPayloadContents($container);
        return $payload;
    }
}
