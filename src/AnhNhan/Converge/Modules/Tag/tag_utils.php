<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Modules\Tag\Views\TagView;

/**
 * Generates a link to a tag using tag view.
 */
function link_tag(Tag $tag)
{
    $tag_view = new TagView($tag->label, $tag->color);
    $link = a($tag_view, urisprintf('tag/%s', $tag->cleanId));
    $link->addClass('tag-link');
    return $link;
}

/**
 * Generates a link to a tag as a hashtag.
 */
function link_hashtag(Tag $tag)
{
    $link = a('#' . $tag->label, urisprintf('tag/%s', $tag->cleanId));
    $link->addClass('tag-link hashtag');
    return $link;
}
