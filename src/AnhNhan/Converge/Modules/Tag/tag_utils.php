<?php

use AnhNhan\Converge as cv;
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
