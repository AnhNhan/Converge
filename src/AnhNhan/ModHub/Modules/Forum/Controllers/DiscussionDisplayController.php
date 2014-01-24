<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\ModHub\Modules\Markup\MarkupEngine;
use AnhNhan\ModHub\Modules\Tag\Views\TagView;
use AnhNhan\ModHub\Views\Grid\Grid;
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
        $request = $this->request;
        $app = $this->app;

        $currentId = $request->request->get("id");

        $disq = id(new DiscussionQuery($this->app))
            ->retrieveDiscussion("DISQ-" . $currentId)
        ;

        $payload = new HtmlPayload;
        $container = new MarkupContainer;

        if ($disq) {
            $grid = new Grid;

            $row = $grid->row();
            $disqColumn = $row->column(9);
            $disqColumn->setId("disq-column");

            $discussionPanel = new Panel;
            $discussionPanel->setId($disq->uid);

            $headerRiff = new MarkupContainer;
            $headerRiff->push(
                ModHub\ht("img")
                    ->addOption("src", ModHub\Modules\User\Storage\User::DEFAULT_PROFILE_IMAGE)
                    ->addClass("user-profile-image")
            );

            $headerContainer = ModHub\ht("div");
            $headerContainer->appendContent(ModHub\ht("h2", $disq->label));

            $small = ModHub\ht("small", ModHub\hsprintf(
                "<strong>%s</strong> created this discussion on %s",
                $disq->authorId,
                $disq->lastActivity->format("D, d M 'y")
            ));

            $headerContainer->appendContent($small);
            $headerRiff->push($headerContainer);
            $discussionPanel->setHeader($headerRiff);

            $discussionPanel->append(ModHub\safeHtml(
                MarkupEngine::fastParse($disq->text)
            ));

            $midriff = $discussionPanel->midriff();
            $tags = mpull($disq->tags->toArray(), "tag");
            if ($tags) {
                foreach ($tags as $tag) {
                    $midriff->push(new TagView($tag->label, $tag->color));
                }
            } else {
                $midriff->push(ModHub\ht("small", "No tags for this discussion")->addClass("muted"));
            }
            $discussionPanel->setMidriffRight(ModHub\ht("a", ModHub\icon_ion("Edit discussion", "edit"))
                    ->addClass("btn btn-info")
                    ->addOption("href", urisprintf("disq/%p/edit", $currentId))
            );

            $disqColumn->push($discussionPanel);

            foreach ($disq->posts->toArray() as $post) {
                $postPanel = new Panel;
                $postPanel->setId($post->uid);

                $title = new MarkupContainer;
                $title->push(
                    ModHub\ht("img")
                        ->addOption("src", ModHub\Modules\User\Storage\User::DEFAULT_PROFILE_IMAGE)
                        ->addClass("user-profile-image")
                );
                $title->push(ModHub\ht("div", $post->modifiedAt->format("D, d M 'y"))->addClass("pull-right"));
                $title->push(ModHub\hsprintf("<div><strong>%s</strong> added a comment</div>", $post->authorId));
                $pxacts = $post->transactions;
                $initialXActCount = 2;
                if (count($pxacts) - $initialXActCount) {
                    $title->push(ModHub\ht("div", ModHub\ht("small", ModHub\hsprintf("This post has received <b>%d</b> modification(s)", count($pxacts) - $initialXActCount))));
                }
                $postPanel->setHeader($title);

                $postPanel->append(
                    ModHub\ht("a", ModHub\icon_ion("edit post", "edit"))
                        ->addClass("btn btn-default btn-small")
                        ->addClass("pull-right")
                        ->addOption("href", urisprintf("disq/%p/%p/edit", $currentId, $post->cleanId))
                );
                $postPanel->append(ModHub\safeHtml(
                    MarkupEngine::fastParse($post->rawText)
                ));

                $disqColumn->push($postPanel);
            }

            $tagColumn = $row->column(3)->addClass("tag-column");

            $tocContainer = new Panel;
            $tocContainer->addClass("forum-toc-affix");
            $tocContainer->setHeader(ModHub\ht("h2", "Table of Contents"));
            $tagColumn->push($tocContainer);

            $ulCont = ModHub\ht("ul")->addClass("nav forum-toc-nav");
            foreach ($disq->posts->toArray() as $post) {
                $ulCont->appendContent(
                    ModHub\ht("li",
                        ModHub\ht("a",
                            ModHub\hsprintf("<em>Post</em> by <strong>%s</strong>", $post->authorId),
                            array("href" => "#" . $post->uid)
                        )
                    )
                    ->addOption("data-toggle", "popover")
                    ->addOption("data-content", phutil_utf8_shorten($post->rawText, 140))
                );
            }
            $tocContainer->append($ulCont);

            $container->push($grid);

            $this->app->getService("resource_manager")
                ->requireJs("application-forum-toc-affix")
                ->requireCss("application-forum-discussion-display")
            ;
        } else {
            $container->push(ModHub\ht("h1", "Could not find a discussion for '" . $currentId . "'"));
        }

        $payload->setPayloadContents($container);
        return $payload;
    }
}
