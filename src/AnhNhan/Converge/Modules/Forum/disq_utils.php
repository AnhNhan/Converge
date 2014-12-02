<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupEngine;
use AnhNhan\Converge\Modules\Forum\Storage\Discussion;
use AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\Converge\Modules\Forum\Storage\PostTransaction;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumListing;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\Converge\Modules\Forum\Views\Objects\PaneledForumListing;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

function render_disq_listing(array $disqs, array $postCounts, $title = null)
{
    $listing = new ForumListing;
    $listing->setTitle($title);
    foreach ($disqs as $discussion) {
        disq_listing_add_object($listing, $discussion, isset($postCounts[$discussion->uid]) ? $postCounts[$discussion->uid]["postcount"] : '?');
    }

    return $listing;
}

function render_disq_paneled_listing(array $disqs, array $postCounts, array $tags, $title = null)
{
    $panelForumListing = id(new PaneledForumListing)
        ->setTitle($title ? cv\ht('h3', $title) : null)
    ;
    foreach ($tags as $t)
    {
        $panelForumListing->addTag($t);
    }

    foreach ($disqs as $discussion) {
        disq_listing_add_object($panelForumListing, $discussion, $postCounts[$discussion->uid]['postcount']);
    }

    return $panelForumListing;
}

function disq_listing_add_object(ForumListing $listing, Discussion $discussion, $postCount)
{
    $object = new ForumObject;
    $object
        ->setHeadline($discussion->label)
        ->setHeadHref('/disq/' . $discussion->cleanId)
        ->postCount($postCount);

    $tags = mpull($discussion->tags->toArray(), 'tag');
    $tags = msort($tags, 'label');
    $tags = array_reverse($tags);
    $tags = msort($tags, 'displayOrder');
    foreach ($tags as $tag) {
        if (!empty($tag)) {
            $object->addTagObject($tag);
        }
    }

    $object->addDetail($discussion->lastActivity->format("D, d M 'y"), 'calendar');
    $object->addDetail(strong(link_user($discussion->author)), 'person-stalker');

    $listing->addObject($object);
}

// Activity

use AnhNhan\Converge\Modules\Activity\Storage\RecordedActivity;

function disq_activity_label(RecordedActivity $activity, $other)
{
    $object_label_link = strong(a(phutil_utf8_shorten($activity->object_label, 40), $activity->object_link));
    $user_link = function () use ($activity, $other)
    {
        $user = idx(idx($other, 'users', []), $activity->xact_contents);
        return $user ? link_user($user) : $activity->xact_contents;
    };
    switch ($activity->xact_type)
    {
        case TransactionEntity::TYPE_CREATE:
            return cv\hsprintf('created discussion %s', $object_label_link);
        case DiscussionTransaction::TYPE_EDIT_TEXT:
            return cv\hsprintf('edited discussion text of %s', $object_label_link);
        default:
            return 'did something';
    }
}

function disq_activity_body(RecordedActivity $activity, $other)
{
    switch ($activity->xact_type)
    {
        case TransactionEntity::TYPE_CREATE:
            return cv\safeHtml(MarkupEngine::fastParse(phutil_utf8_shorten($activity->xact_contents, 160), idx($other, 'markup_rules', [])));
        default:
            return null;
    }
}

function post_activity_label(RecordedActivity $activity, $other)
{
    $object_label_link = strong(a(phutil_utf8_shorten($activity->object_label, 40), $activity->object_link));
    $user_link = function () use ($activity, $other)
    {
        $user = idx(idx($other, 'users', []), $activity->xact_contents);
        return $user ? link_user($user) : $activity->xact_contents;
    };
    switch ($activity->xact_type)
    {
        case TransactionEntity::TYPE_CREATE:
            return cv\hsprintf('posted in %s', $object_label_link);
        case PostTransaction::TYPE_EDIT_POST:
            return cv\hsprintf('edited a post in %s', $object_label_link);
        default:
            return 'did something';
    }
}

function post_activity_body(RecordedActivity $activity, $other)
{
    switch ($activity->xact_type)
    {
        case TransactionEntity::TYPE_CREATE:
            return cv\safeHtml(MarkupEngine::fastParse(phutil_utf8_shorten($activity->xact_contents, 160), idx($other, 'markup_rules', [])));
        default:
            return null;
    }
}

function forum_activity_class(RecordedActivity $activity, array $other = [])
{
    switch ($activity->xact_type)
    {
        case TransactionEntity::TYPE_CREATE:
        case PostTransaction::TYPE_EDIT_POST:
        case DiscussionTransaction::TYPE_EDIT_TEXT:
            return 'activity-content';
        default:
            return null;
    }
}
