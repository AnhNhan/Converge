<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\ModHub\Modules\Forum\Views\Display\Discussion as DiscussionView;
use AnhNhan\ModHub\Modules\Forum\Views\Display\Post as PostView;
use AnhNhan\ModHub\Modules\Markup\MarkupEngine;
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

        $query = new DiscussionQuery($this->app);
        $disq = $query
            ->retrieveDiscussion("DISQ-" . $currentId)
        ;

        $payload = new HtmlPayload;
        $container = new MarkupContainer;

        if ($disq) {
            $grid = new Grid;

            $row = $grid->row();
            $disqColumn = $row->column(9);
            $disqColumn->setId("disq-column");

            $discussionView = id(new DiscussionView)
                ->setId($disq->uid)
                ->setHeader($disq->label)
                ->setDate($disq->lastActivity->format("D, d M 'y"))
                ->setUserDetails($disq->authorId, ModHub\Modules\User\Storage\User::generateGravatarImagePath($disq->authorId, 63))
                ->setBodyText(ModHub\safeHtml(
                    MarkupEngine::fastParse($disq->text)
                ))
                ->addButton(
                    ModHub\ht("a", ModHub\icon_ion("Edit discussion", "edit"))
                        ->addClass("btn btn-info")
                        ->addOption("href", urisprintf("disq/%p/edit", $currentId))
                )
            ;

            $tags = mpull($disq->tags->toArray(), "tag");
            if ($tags) {
                foreach ($tags as $tag) {
                    $discussionView->addTag($tag->label, $tag->color);
                }
            }

            $disqColumn->push($discussionView);

            $tocExtractor = new \AnhNhan\ModHub\Modules\Markup\TOCExtractor;
            $tocs = array();

            $page_nr = 1;
            $page_size = 20;

            if ($request->request->has("page-nr") && ($r_page_nr = $request->request->get("page-nr")) && preg_match("/^\\d+$/", $r_page_nr)) {
                $page_nr = $r_page_nr;
            }

            $offset = ($page_nr - 1) * $page_size;

            // $transactions = $query->getPaginatorForDiscussionTransactions($disq->uid, $page_size, $offset)->getIterator()->getArrayCopy();
            $transactions = $disq->transactions->slice($offset, $page_size);
            $transactions_grouped = mgroup($transactions, "type");
            $post_ids = mpull(idx($transactions_grouped, DiscussionTransaction::TYPE_ADD_POST, array()), "newValue");
            $posts = $query->retrievePostsForIDs($post_ids);

            // Manual GC
            unset($transactions_sorted);
            unset($post_ids);

            foreach ($posts as $post) {
                list($toc, $markup) = $tocExtractor->parseExtractAndProcess($post->rawText);
                $tocs[$post->uid] = $toc;

                $postView = new PostView;
                $postView
                    ->setId($post->uid)
                    ->setUserDetails($post->authorId, ModHub\Modules\User\Storage\User::generateGravatarImagePath($post->authorId, 42))
                    ->setDate($post->modifiedAt->format("D, d M 'y"))
                    ->addButton(
                        ModHub\ht("a", ModHub\icon_ion("edit post", "edit"))
                            ->addClass("btn btn-default btn-small")
                            ->addClass("pull-right")
                            ->addOption("href", urisprintf("disq/%p/%p/edit", $currentId, $post->cleanId))
                    )
                    ->setBodyText(ModHub\safeHtml($markup))
                ;

                $disqColumn->push($postView);
            }

            $tagColumn = $row->column(3)->addClass("tag-column");

            $tocContainer = new Panel;
            $tocContainer->addClass("forum-toc-affix");
            $tocContainer->setHeader(ModHub\ht("h2", "Table of Contents"));
            $tagColumn->push($tocContainer);

            $ulCont = ModHub\ht("ul")->addClass("nav forum-toc-nav");
            foreach ($posts as $post) {
                $entry =
                    ModHub\ht("li",
                        ModHub\ht("a",
                            ModHub\hsprintf("<em>Post</em> by <strong>%s</strong>", $post->authorId),
                            array("href" => "#" . $post->uid)
                        )
                    )
                    ->addOption("data-toggle", "popover")
                    ->addOption("data-content", phutil_utf8_shorten($post->rawText, 140))
                ;

                $subToc = idx($tocs, $post->uid);
                if ($subToc) {
                    $subUl = ModHub\ht("ul")->addClass("subtoc");
                    foreach ($subToc as $tt) {
                        $subUl->appendContent(ModHub\hsprintf(
                            "<li class=\"subtoc-%s\"><a style=\"padding-left: %fem;\" href=\"#%s\">%s</a></li>",
                            $tt["type"],
                            $tt["level"] + 1.5,
                            $tt["hash"],
                            $tt["text"]
                        ));
                    }

                    $entry->appendContent($subUl);
                }

                $ulCont->appendContent($entry);
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
