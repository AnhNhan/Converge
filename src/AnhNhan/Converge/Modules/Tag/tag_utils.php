<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Tag\TagQuery;
use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Modules\Tag\Views\TagView;

const TagLinkExtra_None      = 'tag.extra.none';
const TagLinkExtra_Tooltip   = 'tag.extra.tooltip';
const TagLinkExtra_Hovercard = 'tag.extra.hovercard';

/**
 * Generates a link to a tag using tag view.
 */
function link_tag(Tag $tag, $extra = TagLinkExtra_Tooltip)
{
    $tag_view = new TagView($tag->label, $tag->color);
    $link = a($tag_view, urisprintf('tag/%s', $tag->cleanId));
    $link->addClass('tag-link');
    if ($extra == TagLinkExtra_Tooltip)
    {
        $link->addOption('data-toggle', 'tooltip');
        $link->addOption('title', $tag->description);
    }
    return $link;
}

/**
 * Generates a link to a tag as a hashtag.
 */
function link_hashtag(Tag $tag, $extra = UserLinkExtra_Tooltip)
{
    $link = a('#' . $tag->label, urisprintf('tag/%s', $tag->cleanId));
    $link->addClass('tag-link hashtag');
    return $link;
}

function create_tag_query($app_or_em)
{
    return new TagQuery($app_or_em);
}

function fetch_external_tags(array $assoc_tags, TagQuery $query, $id_field = 'tagId', $set_method = 'setTag', $tag_field = 'tag')
{
    if (empty($assoc_tags))
    {
        return;
    }

    try
    {
        pull($assoc_tags, function ($assoc_tag) { return $assoc_tag->tag_field; });
        // We could successfully traverse the whole array - we have all loaded
        return;
    }
    catch (\Exception $e)
    {
        // <do nothing>
    }

    $tag_ids = mpull($assoc_tags, $id_field);
    $tag_ids = array_unique($tag_ids);
    $tags = mkey($query->retrieveTagsForIDs($tag_ids), 'uid');
    foreach ($assoc_tags as $thing)
    {
        $thing->$set_method(idx($tags, $thing->$id_field));
    }
}

