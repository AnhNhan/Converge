<?php
namespace AnhNhan\Converge\Modules\Forum\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\Converge\Modules\Markup\MarkupEngine;
use AnhNhan\Converge\Modules\Tag\TagQuery;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionDisplayController extends AbstractForumController
{
    const CreateXactHide_GraceTime = 120;

    public function handle()
    {
        $request = $this->request;
        $app = $this->app;

        $currentId = $request->request->get("id");

        $query = $this->buildQuery();
        $disq = $query
            ->retrieveDiscussion("DISQ-" . $currentId)
        ;

        if (!$disq)
        {
            return id(new ResponseHtml404)->setText('Could not find a discussion for \'' . $currentId . '\'');
        }

        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setTitle($disq->label);
        $payload->setPayloadContents($container);

        $grid = grid();
        $row  = $grid->row();
        $disqColumn = $row->column(9)->setId("disq-column");
        $tagColumn  = $row->column(3)->addClass("tag-column");

        $tocExtractor = new \AnhNhan\Converge\Modules\Markup\TOCExtractor;
        $tocs = [];
        $markups = [];

        $page_nr = 1;
        $page_size = 30;

        if ($request->request->has("page-nr") && ($r_page_nr = $request->request->get("page-nr")) && preg_match("/^\\d+$/", $r_page_nr)) {
            $page_nr = $r_page_nr;
        }

        $offset = ($page_nr - 1) * $page_size;

        $transactions = $disq->transactions->slice($offset, $page_size);
        $transactions_grouped = mgroup($transactions, "type");
        $post_ids = mpull(idx($transactions_grouped, DiscussionTransaction::TYPE_ADD_POST, []), "newValue");
        $posts = $query->retrievePostsForIDs($post_ids) ?: [];
        $posts = mpull($posts, null, "uid");
        $query->fetchExternalUsers(array_merge($transactions, $posts, [$disq]));
        $query->fetchExternalsForDiscussions([$disq]);

        $tagQuery = new TagQuery($this->externalApp('tag'));
        $tag_ids = array_unique(array_merge(
            mpull(idx($transactions_grouped, DiscussionTransaction::TYPE_ADD_TAG, []), "newValue"),
            mpull(idx($transactions_grouped, DiscussionTransaction::TYPE_REMOVE_TAG, []), "oldValue")
        ));
        $tags = $tagQuery->retrieveTagsForIDs($tag_ids);
        $tags = mpull($tags, null, "uid");

        $create_xact = idx($transactions_grouped, DiscussionTransaction::TYPE_CREATE);
        $create_date = null;
        if ($create_xact) {
            $create_xact = head($create_xact);
            $create_date = $create_xact->createdAt->getTimestamp();

            // Prepend create-xact to the stack
            // First remove, then merge with create-xact as first
            unset($transactions[array_search($create_xact, $transactions)]);
            $transactions = array_merge(
                [$create_xact],
                $transactions
            );
        }

        $post_xacts = idx($transactions_grouped, DiscussionTransaction::TYPE_ADD_POST, []);

        // Manual GC
        unset($transactions_grouped);
        unset($post_ids);
        unset($tag_ids);

        foreach (array_merge($posts, $create_xact ? [$disq] : []) as $post) {
            list($toc, $markup) = $tocExtractor->parseExtractAndProcess($post->rawText);
            $tocs[$post->uid] = $toc;
            $markups[$post->uid] = $markup;
        }

        $disqPanel = null;
        if ($create_xact) {
            $disqPanel = renderDiscussion($disq, $markups[$disq->uid])->getProcessed();
            $disqColumn->push($disqPanel);
        }

        foreach ($post_xacts as $post_xact) {
            $subject_uid = $post_xact->newValue;
            $post   = $posts[$subject_uid];
            $markup = $markups[$subject_uid];
            $disqColumn->push(renderPost($post, $markup));
        }

        if ($disqPanel)
        {
            $xacts = array_filter($transactions, function ($xact) use ($create_date, $create_xact)
            {
                return !(
                    $create_date
                    && $create_date + self::CreateXactHide_GraceTime > $xact->createdAt->getTimestamp()
                    && $xact->actorId == $create_xact->actorId
                );
            });
            attach_xacts($disqPanel, $xacts, $tags);
        }

        $tocContainer = panel(h2('Table of Contents'), 'forum-toc-affix');
        $tocContainer->addClass("forum-toc-affix");
        $tagColumn->push($tocContainer);

        $ulCont = Converge\ht("ul")->addClass("nav forum-toc-nav");
        foreach ($transactions as $xact) {
            if ($xact->type == DiscussionTransaction::TYPE_CREATE) {
                // TODO: Sub-ToC
                $ulCont->append(
                    popover("li",
                        a(
                            Converge\hsprintf("<em>Discussion</em> by <strong>%s</strong>", $disq->author->name),
                            "#" . $disq->uid
                        ),
                        phutil_utf8_shorten($disq->rawText, 140)
                    )
                );
                continue;
            }

            if ($xact->type != DiscussionTransaction::TYPE_ADD_POST) {
                continue;
            }

            // Only post-type left
            $post = $posts[$xact->newValue];

            if ($post->deleted) {
                $entry = Converge\ht("li",
                    a(Converge\hsprintf("<em>Post</em> deleted"), "#" . hash_hmac("sha512", $post->uid, time()))
                );
                $ulCont->append($entry);
                continue;
            }

            $entry =
                popover("li",
                    a(
                        Converge\hsprintf("<em>Post</em> by <strong>%s</strong>", $post->author->name),
                        "#" . $post->uid
                    ),
                    phutil_utf8_shorten($post->rawText, 140)
                )
            ;

            $subToc = idx($tocs, $post->uid);
            if ($subToc) {
                $subUl = Converge\ht("ul")->addClass("subtoc");
                foreach ($subToc as $tt) {
                    $subUl->append(Converge\hsprintf(
                        "<li class=\"subtoc-%s\"><a style=\"padding-left: %fem;\" href=\"#%s\">%s</a></li>",
                        $tt["type"],
                        $tt["level"] + 1.5,
                        $tt["hash"],
                        $tt["text"]
                    ));
                }

                $entry->append($subUl);
            }

            $ulCont->append($entry);
        }
        $tocContainer->append($ulCont);

        $container->push($grid);

        $this->app->getService("resource_manager")
            ->requireJs("application-forum-toc-affix")
            ->requireJs("application-forum-show-changes")
            ->requireCss("application-forum-discussion-display")
            ->requireCss("application-diff")
        ;

        return $payload;
    }
}
