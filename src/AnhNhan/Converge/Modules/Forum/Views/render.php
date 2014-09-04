<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\Converge\Modules\Forum\Views\Display\Discussion as DiscussionView;
use AnhNhan\Converge\Modules\Forum\Views\Display\DeletedPost as DeletedPostView;
use AnhNhan\Converge\Modules\Forum\Views\Display\Post as PostView;
use AnhNhan\Converge\Modules\Forum\Views\Display\TagAdd as TagAddedView;
use AnhNhan\Converge\Modules\Forum\Views\Display\TagRemove as TagRemovedView;
use AnhNhan\Converge\Modules\Forum\Views\Display\TextChangeLabel as LabelChangeView;
use AnhNhan\Converge\Modules\Forum\Views\Display\TextChangeText as TextChangeView;

function renderDiscussion($disq, $markup)
{
    $discussionView = id(new DiscussionView)
        ->setId($disq->uid)
        ->setHeader($disq->label)
        ->setDate($disq->lastActivity->format("D, d M 'y"))
        ->setUserDetails(link_user($disq->author), $disq->author->getGravatarImagePath(75))
        ->setBodyText(cv\safeHtml(
            $markup
        ))
        ->addButton(
            cv\ht("a", cv\icon_ion("Edit discussion", "edit"))
                ->addClass("btn btn-info")
                ->addOption("href", urisprintf("disq/%p/edit", $disq->cleanId))
        )
    ;

    $tags = mpull($disq->tags->toArray(), "tag");
    $tags = msort($tags, "label");
    $tags = array_reverse($tags);
    $tags = msort($tags, "displayOrder");
    if ($tags) {
        foreach ($tags as $tag) {
            $discussionView->addTagObject($tag);
        }
    }

    $panel = $discussionView->render();
    $panel->setId($disq->uid);
    array_map([$panel, 'addComment'], $disq->comments->toArray());
    return $panel;
}

function renderPost($post, $markup)
{
    if ($post->deleted) {
        return panel()
            ->setHeader(div('', h3(cv\icon_ion('Deleted Post ', 'close', false))->append(cv\ht('small', $post->createdAt->format("D, d M 'y"))->addClass('minor-stuff'))))
            ->setId(hash_hmac("sha512", $post->uid, time())) // Fuzzy id
            ->addClass("post-deleted")
        ;
    }

    $postView = new PostView;
    $postView
        ->setId($post->uid)
        ->setUserDetails(link_user($post->author), $post->author->getGravatarImagePath(52))
        ->setDate($post->createdAt->format("D, d M 'y"))
        ->addButton(
            cv\ht("a", cv\icon_ion("edit post", "edit"))
                ->addClass("btn btn-default btn-small")
                ->addOption("href", urisprintf("disq/%p/%p/edit", str_replace("DISQ-", "", $post->parentDisqId), $post->cleanId))
        )
        ->setBodyText(cv\safeHtml($markup))
    ;

    $panel = $postView->render();
    $panel->setId($post->uid);
    array_map([$panel, 'addComment'], $post->comments->toArray());
    return $panel;
}

function attach_xacts($post_container, array $transactions, array $tags)
{
    $xactContainer = div("xact-container");

    foreach ($transactions as $xact) {
        $ss = [DiscussionTransaction::TYPE_CREATE => true, DiscussionTransaction::TYPE_ADD_POST => true];
        if (isset($ss[$xact->type])) {
            continue;
        }

        $subject_uid = $xact->newValue;
        switch ($xact->type) {
            case DiscussionTransaction::TYPE_ADD_TAG:
                $xactContainer->append(
                    id(new TagAddedView)
                        ->setId($xact->uid)
                        ->setUserDetails(link_user($xact->actor), $xact->actor->getGravatarImagePath(37))
                        ->setDate($xact->createdAt->format("D, d M 'y"))
                        ->addTag($tags[$subject_uid])
                );
                break;
            case DiscussionTransaction::TYPE_REMOVE_TAG:
                $subject_uid = $xact->oldValue;
                $xactContainer->append(
                    id(new TagRemovedView)
                        ->setId($xact->uid)
                        ->setUserDetails(link_user($xact->actor), $xact->actor->getGravatarImagePath(37))
                        ->setDate($xact->createdAt->format("D, d M 'y"))
                        ->addTag($tags[$subject_uid])
                );
                break;
            case DiscussionTransaction::TYPE_EDIT_LABEL:
            case DiscussionTransaction::TYPE_EDIT_TEXT:
                $viewObj = $xact->type == DiscussionTransaction::TYPE_EDIT_LABEL ?
                    new LabelChangeView :
                    new TextChangeView;
                $xactContainer->append(
                    $viewObj
                        ->setId($xact->uid)
                        ->setUserDetails(link_user($xact->actor), $xact->actor->getGravatarImagePath(37))
                        ->setDate($xact->createdAt->format("D, d M 'y"))
                        ->setPrevText($xact->oldValue)
                        ->setNextText($xact->newValue)
                );
                break;

            default:
                throw new \Exception("Unknown transaction type: '{$xact->type}'");
                break;
        }
    }

    if (!$xactContainer->isSelfClosing())
    {
        $disqXactListing = div("xact-listing");
        $disqXactListing->append(h2("Changes", "xact-listing-header"));
        $disqXactListing->append(
            a("show changes")
                ->addClass("btn btn-default")
                ->addClass("show-changes-btn")
        );
        $disqXactListing->append(
            a("hide changes")
                ->addClass("btn btn-default")
                ->addClass("hide-changes-btn")
        );
        $disqXactListing->append($xactContainer);
        $post_container->append($disqXactListing);
    }
}
