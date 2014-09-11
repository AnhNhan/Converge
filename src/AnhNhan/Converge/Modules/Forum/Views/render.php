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
            a(cv\icon_ion('edit discussion', 'edit'), urisprintf('disq/%p/edit', $disq->cleanId))
                ->addClass('btn btn-info')
        )
    ;

    $tags = mpull($disq->tags->toArray(), 'tag');
    $tags = msort($tags, 'label');
    $tags = array_reverse($tags);
    $tags = msort($tags, 'displayOrder');
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
            ->setId(hash_hmac('sha512', $post->uid, time())) // Fuzzy id
            ->addClass("post-deleted")
        ;
    }

    $postView = new PostView;
    $postView
        ->setId($post->uid)
        ->setUserDetails(link_user($post->author), $post->author->getGravatarImagePath(52))
        ->setDate($post->createdAt->format("D, d M 'y"))
        ->addButton(
            a(cv\icon_ion('edit post', 'edit'), urisprintf('disq/%p/%p/edit', str_replace('DISQ-', '', $post->parentDisqId), $post->cleanId))
                ->addClass('btn btn-default btn-small')
        )
        ->setBodyText(cv\safeHtml($markup))
    ;

    $panel = $postView->render();
    $panel->setId($post->uid);
    array_map([$panel, 'addComment'], $post->comments->toArray());
    return $panel;
}
