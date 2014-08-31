<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupEngine;
use AnhNhan\Converge\Modules\Newsroom\Storage\ArticleTransaction;
use AnhNhan\Converge\Modules\Newsroom\Storage\ChannelTransaction;
use AnhNhan\Converge\Modules\Newsroom\Storage\DMArticleTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

// Activity

use AnhNhan\Converge\Modules\Activity\Storage\RecordedActivity;

function article_activity_label(RecordedActivity $activity, array $other = [])
{
    // safeHtml, since we save label as HTML in the DB
    $object_label_link = strong(a(cv\safeHtml($activity->object_label), $activity->object_link));
    switch ($activity->xact_type)
    {
        case TransactionEntity::TYPE_CREATE:
            return cv\hsprintf('created article %s', $object_label_link);
        case DMArticleTransaction::TYPE_EDIT_TEXT:
            return cv\hsprintf('updated article %s', $object_label_link);
        case ArticleTransaction::TYPE_EDIT_TITLE:
            $old_label_link = strong(a($activity->xact_contents, $activity->object_link));
            return cv\hsprintf('renamed article %s to %s', $old_label_link, $object_label_link);
        default:
            return 'did something';
    }
}

function article_activity_body(RecordedActivity $activity, array $other = [])
{
    switch ($activity->xact_type)
    {
        case TransactionEntity::TYPE_CREATE:
            return cv\safeHtml(MarkupEngine::fastParse(phutil_utf8_shorten($activity->xact_contents, 160), idx($other, 'markup_rules', [])));
        default:
            return null;
    }
}

function article_activity_class(RecordedActivity $activity, array $other = [])
{
    switch ($activity->xact_type)
    {
        case TransactionEntity::TYPE_CREATE:
        case DMArticleTransaction::TYPE_EDIT_TEXT:
            return 'activity-content';
        default:
            return null;
    }
}

function channel_activity_label(RecordedActivity $activity, array $other = [])
{
    $object_label_link = strong(a(phutil_utf8_shorten($activity->object_label, 40), $activity->object_link));
    switch ($activity->xact_type)
    {
        case TransactionEntity::TYPE_CREATE:
            return cv\hsprintf('created newsrom channel %s', $object_label_link);
        case ChannelTransaction::TYPE_EDIT_LABEL:
            $old_label_link = strong(a(phutil_utf8_shorten($activity->xact_contents, 40), $activity->object_link));
            return cv\hsprintf('renamed newsroom channel %s to %s', $old_label_link, $object_label_link);
        default:
            return 'did something';
    }
}

