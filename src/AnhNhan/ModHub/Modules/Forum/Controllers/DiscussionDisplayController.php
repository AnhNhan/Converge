<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\ModHub\Modules\Forum\Views\Display\Discussion as DiscussionView;
use AnhNhan\ModHub\Modules\Forum\Views\Display\DeletedPost as DeletedPostView;
use AnhNhan\ModHub\Modules\Forum\Views\Display\Post as PostView;
use AnhNhan\ModHub\Modules\Forum\Views\Display\TagAdd as TagAddedView;
use AnhNhan\ModHub\Modules\Forum\Views\Display\TagRemove as TagRemovedView;
use AnhNhan\ModHub\Modules\Forum\Views\Display\TextChangeLabel as LabelChangeView;
use AnhNhan\ModHub\Modules\Forum\Views\Display\TextChangeText as TextChangeView;
use AnhNhan\ModHub\Modules\Markup\MarkupEngine;
use AnhNhan\ModHub\Modules\Tag\TagQuery;
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

        $query = $this->buildQuery();
        $disq = $query
            ->retrieveDiscussion("DISQ-" . $currentId)
        ;

        $payload = new HtmlPayload;
        $container = new MarkupContainer;

        if ($disq) {
            $query->fetchExternalsForDiscussions(array($disq));

            $grid = new Grid;

            $row = $grid->row();
            $disqColumn = $row->column(9);
            $disqColumn->setId("disq-column");

            $payload->setTitle($disq->label);

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
            $markups = array();

            $page_nr = 1;
            $page_size = 20;

            if ($request->request->has("page-nr") && ($r_page_nr = $request->request->get("page-nr")) && preg_match("/^\\d+$/", $r_page_nr)) {
                $page_nr = $r_page_nr;
            }

            $offset = ($page_nr - 1) * $page_size;

            $transactions = $disq->transactions->slice($offset, $page_size);
            $transactions_grouped = mgroup($transactions, "type");
            $post_ids = mpull(idx($transactions_grouped, DiscussionTransaction::TYPE_ADD_POST, array()), "newValue");
            $posts = $query->retrievePostsForIDs($post_ids) ?: array();
            $posts = mpull($posts, null, "uid");

            $tagQuery = new TagQuery($this->app->getService("app.list")->app("tag")->getEntityManager());
            $tag_ids = array_unique(array_merge(
                mpull(idx($transactions_grouped, DiscussionTransaction::TYPE_ADD_TAG, array()), "newValue"),
                mpull(idx($transactions_grouped, DiscussionTransaction::TYPE_REMOVE_TAG, array()), "oldValue")
            ));
            $tags = $tagQuery->retrieveTagsForIDs($tag_ids);
            $tags = mpull($tags, null, "uid");

            $create_xact = idx($transactions_grouped, DiscussionTransaction::TYPE_CREATE);
            $create_date = null;
            if ($create_xact) {
                $create_xact = head($create_xact);
                $create_date = $create_xact->createdAt->getTimestamp();
            }

            // Manual GC
            unset($transactions_grouped);
            unset($post_ids);
            unset($tag_ids);

            foreach ($posts as $post) {
                list($toc, $markup) = $tocExtractor->parseExtractAndProcess($post->rawText);
                $tocs[$post->uid] = $toc;
                $markups[$post->uid] = $markup;
            }

            foreach ($transactions as $xact) {
                if ($create_date && $create_date == $xact->createdAt->getTimestamp()) {
                    continue;
                }

                $subject_uid = $xact->newValue;
                $actor = $xact->actorId;
                switch ($xact->type) {
                    case DiscussionTransaction::TYPE_ADD_POST:
                        $post   = $posts[$subject_uid];
                        $markup = $markups[$subject_uid];
                        $disqColumn->push($this->renderPost($post, $markup));
                        break;
                    case DiscussionTransaction::TYPE_ADD_TAG:
                        $disqColumn->push(
                            id(new TagAddedView)
                                ->setId($subject_uid)
                                ->setUserDetails($actor, ModHub\Modules\User\Storage\User::generateGravatarImagePath($actor, 42))
                                ->setDate($xact->createdAt->format("D, d M 'y"))
                                ->addTag($tags[$subject_uid])
                        );
                        break;
                    case DiscussionTransaction::TYPE_REMOVE_TAG:
                        $subject_uid = $xact->oldValue;
                        $disqColumn->push(
                            id(new TagRemovedView)
                                ->setId($subject_uid)
                                ->setUserDetails($actor, ModHub\Modules\User\Storage\User::generateGravatarImagePath($actor, 42))
                                ->setDate($xact->createdAt->format("D, d M 'y"))
                                ->addTag($tags[$subject_uid])
                        );
                        break;
                    case DiscussionTransaction::TYPE_EDIT_LABEL:
                    case DiscussionTransaction::TYPE_EDIT_TEXT:
                        $viewObj = $xact->type == DiscussionTransaction::TYPE_EDIT_LABEL ?
                            new LabelChangeView :
                            new TextChangeView;
                        $disqColumn->push(
                            $viewObj
                                ->setUserDetails($actor, ModHub\Modules\User\Storage\User::generateGravatarImagePath($actor, 42))
                                ->setDate($xact->createdAt->format("D, d M 'y"))
                                ->setPrevText($xact->oldValue)
                                ->setNextText($xact->newValue)
                        );
                        break;
                    case DiscussionTransaction::TYPE_CREATE:
                        // <ignore>
                        break;

                    default:
                        throw new \Exception("Unknown transaction type: '{$xact->type}'");
                        break;
                }
            }

            $tagColumn = $row->column(3)->addClass("tag-column");

            $tocContainer = new Panel;
            $tocContainer->addClass("forum-toc-affix");
            $tocContainer->setHeader(ModHub\ht("h2", "Table of Contents"));
            $tagColumn->push($tocContainer);

            $ulCont = ModHub\ht("ul")->addClass("nav forum-toc-nav");
            foreach ($posts as $post) {
                if ($post->deleted) {
                    $entry = ModHub\ht("li",
                        a(ModHub\hsprintf("<em>Post</em> deleted"), "#" . hash_hmac("sha512", $post->uid, time()))
                    );
                    $ulCont->appendContent($entry);
                    continue;
                }

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
                ->requireCss("application-diff")
            ;
        } else {
            $container->push(ModHub\ht("h1", "Could not find a discussion for '" . $currentId . "'"));
        }

        $payload->setPayloadContents($container);
        return $payload;
    }

    private function renderPost($post, $markup)
    {
        if ($post->deleted) {
            return id(new DeletedPostView)
                ->setId(hash_hmac("sha512", $post->uid, time())) // Fuzzy id
                ->addClass("post-deleted")
                ->setDate($post->createdAt->format("D, d M 'y"))
            ;
        }

        $postView = new PostView;
        $postView
            ->setId($post->uid)
            ->setUserDetails($post->authorId, ModHub\Modules\User\Storage\User::generateGravatarImagePath($post->authorId, 42))
            ->setDate($post->createdAt->format("D, d M 'y"))
            ->addButton(
                ModHub\ht("a", ModHub\icon_ion("edit post", "edit"))
                    ->addClass("btn btn-default btn-small")
                    ->addClass("pull-right")
                    ->addOption("href", urisprintf("disq/%p/%p/edit", $post->parentDisqId, $post->cleanId))
            )
            ->setBodyText(ModHub\safeHtml($markup))
        ;

        return $postView;
    }
}
