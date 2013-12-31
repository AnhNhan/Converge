<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Markup\MarkupEngine;
use AnhNhan\ModHub\Views\Panel\Panel;
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

        $currentId = $request->request->get("id");

        $forumEntityManager = $app->getEntityManager();
        $disqRepo = $forumEntityManager->getRepository("AnhNhan\\ModHub\\Modules\\Forum\\Storage\\Discussion");

        /**
         * @var \AnhNhan\ModHub\Modules\Forum\Storage\Discussion
         */
        $disq = $disqRepo->find("DISQ-" . $currentId);

        $payload = new HtmlPayload;
        $container = new MarkupContainer;

        if ($disq) {
            $dangerPanel = new Panel;
            $dangerPanel
                ->setColor(Panel::COLOR_DANGER)
                ->setHeader(ModHub\ht("h3", "Warning!"))
                ->append("The discussion display page may contain unsafe HTML!")
            ;
            $container->push($dangerPanel);

            $discussionPanel = new Panel;
            $discussionPanel->setHeader(ModHub\ht("h2", $disq->label()));

            $midRiff = $discussionPanel->midriff();
            $midRiff->push(ModHub\ht("strong", $disq->authorId()));
            $midRiff->push(ModHub\ht("span", " created this discussion on "));
            $midRiff->push(ModHub\ht("span", $disq->lastActivity()->format("D, d M 'y")));

            // Warning! Unsafe HTML!
            $discussionPanel->append(ModHub\safeHtml(MarkupEngine::fastParse($disq->text())));

            $container->push($discussionPanel);

            foreach ($disq->posts()->toArray() as $post) {
                $postPanel = new Panel;
                $title = $postPanel->midriff();
                $title->push(ModHub\ht("strong", $post->authorId()));
                $title->push(ModHub\ht("span", " added a comment"));
                $postPanel->setMidriffRight($post->modifiedAt()->format("D, d M 'y"));
                // Warning! Unsafe HTML!
                $postPanel->append(ModHub\safeHtml(MarkupEngine::fastParse($post->rawText())));

                $container->push($postPanel);
            }
        } else {
            $container->push(ModHub\ht("h1", "Could not find a discussion for '" . $currentId . "'"));
        }

        $payload->setPayloadContents($container);
        return $payload;
    }
}
