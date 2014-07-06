<?php

use AnhNhan\ModHub\Modules\Forum\Views\Display\Discussion as DiscussionView;
use AnhNhan\ModHub\Modules\Forum\Views\Display\DeletedPost as DeletedPostView;
use AnhNhan\ModHub\Modules\Forum\Views\Display\Post as PostView;

function renderDiscussion($disq, $markup)
{
    $discussionView = id(new DiscussionView)
        ->setId($disq->uid)
        ->setHeader($disq->label)
        ->setDate($disq->lastActivity->format("D, d M 'y"))
        ->setUserDetails($disq->authorId, AnhNhan\ModHub\Modules\User\Storage\User::generateGravatarImagePath($disq->authorId, 63))
        ->setBodyText(AnhNhan\ModHub\safeHtml(
            $markup
        ))
        ->addButton(
            AnhNhan\ModHub\ht("a", AnhNhan\ModHub\icon_ion("Edit discussion", "edit"))
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
            $discussionView->addTag($tag->label, $tag->color);
        }
    }

    return $discussionView;
}

function renderPost($post, $markup)
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
        ->setUserDetails($post->authorId, AnhNhan\ModHub\Modules\User\Storage\User::generateGravatarImagePath($post->authorId, 42))
        ->setDate($post->createdAt->format("D, d M 'y"))
        ->addButton(
            AnhNhan\ModHub\ht("a", AnhNhan\ModHub\icon_ion("edit post", "edit"))
                ->addClass("btn btn-default btn-small")
                ->addClass("pull-right")
                ->addOption("href", urisprintf("disq/%p/%p/edit", str_replace("DISQ-", "", $post->parentDisqId), $post->cleanId))
        )
        ->setBodyText(AnhNhan\ModHub\safeHtml($markup))
    ;

    return $postView;
}
