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
        $request = $this->request();
        $app = $this->app();

        $currentId = $request->request->get("id");

        $disq = id(new DiscussionQuery($this->app()))
            ->retrieveDiscussion("DISQ-" . $currentId)
        ;

        $payload = new HtmlPayload;
        $container = new MarkupContainer;

        if ($disq) {
            $grid = new Grid;

            $dangerRow  = $grid->row();
            $dangerColumn = $dangerRow->column(12);
            $dangerPanel = new Panel;
            $dangerPanel
                ->setColor(Panel::COLOR_DANGER)
                ->setHeader(ModHub\ht("h3", "Warning!"))
                ->append("The discussion display page may contain unsafe HTML!")
            ;
            $dangerColumn->push($dangerPanel);

            $row = $grid->row();
            $disqColumn = $row->column(9);
            $disqColumn->setId("disq-column");

            $discussionPanel = new Panel;
            $discussionPanel->setHeader(ModHub\ht("h2", $disq->label()));

            $midRiff = $discussionPanel->midriff();
            $midRiff->push(ModHub\ht("strong", $disq->authorId()));
            $midRiff->push(ModHub\ht("span", " created this discussion on "));
            $midRiff->push(ModHub\ht("span", $disq->lastActivity()->format("D, d M 'y")));

            // Warning! Unsafe HTML!
            $discussionPanel->append(ModHub\safeHtml(MarkupEngine::fastParse($disq->text())));

            $disqColumn->push($discussionPanel);

            foreach ($disq->posts()->toArray() as $post) {
                $postPanel = new Panel;
                $postPanel->setId($post->uid());
                $title = $postPanel->midriff();
                $title->push(ModHub\ht("strong", $post->authorId()));
                $title->push(ModHub\ht("span", " added a comment"));
                $postPanel->setMidriffRight($post->modifiedAt()->format("D, d M 'y"));
                // Warning! Unsafe HTML!
                $postPanel->append(ModHub\safeHtml(MarkupEngine::fastParse($post->rawText())));

                //$disqColumn->push(ModHub\ht("a", $post->uid(), array("anchor" => $post->uid()))->addClass("hidden"));
                $disqColumn->push($postPanel);
            }

            $tagColumn = $row->column(3)->addClass("tag-column");
            $tagContainer = new Panel;
            $tagContainer->setHeader(ModHub\ht("h2", "Tags"));
            $tagColumn->push($tagContainer);
            $tags = mpull($disq->tags()->toArray(), "tag");
            foreach ($tags as $tag) {
                $tagContainer->append(new TagView($tag->label(), $tag->color()));
            }

            $linksContainer = new Panel;
            $tagColumn->push($linksContainer);
            $linksContainer->append(ModHub\ht("a", "Add post")->addClass("btn btn-success")->addOption("href", "disq/{$currentId}?mode=post"));
            $linksContainer->append(ModHub\ht("a", "Edit discussion")->addClass("btn btn-info")->addOption("href", "disq/{$currentId}?mode=edit"));

            $tocContainer = new Panel;
            $tocContainer->addClass("forum-toc-affix");
            $tocContainer->setHeader(ModHub\ht("h2", "Table of Contents"));
            $tagColumn->push($tocContainer);

            $ulCont = ModHub\ht("ul")->addClass("nav forum-toc-nav");
            foreach ($disq->posts()->toArray() as $post) {
                $ulCont->appendContent(
                    ModHub\ht("li",
                        ModHub\ht("a",
                            ModHub\safeHtml(sprintf("<em>Post</em> by <strong>%s</strong>", $post->authorId())),
                            array("href" => /* $request->getRequestUri() .*/ "#" . $post->uid())
                        )
                    )
                    ->addOption("data-toggle", "popover")
                    ->addOption("data-content", phutil_utf8_shorten($post->rawText(), 140))
                );
            }
            $tocContainer->append($ulCont);

            $container->push($grid);

            $this->app()->getService("resource_manager")
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
