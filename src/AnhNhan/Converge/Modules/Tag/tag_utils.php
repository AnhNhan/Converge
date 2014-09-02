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
function link_tag(Tag $tag, $extra = TagLinkExtra_None)
{
    $tag_view = render_tag($tag);
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
function link_hashtag(Tag $tag, $extra = TagLinkExtra_None)
{
    $link = a('#' . $tag->label, urisprintf('tag/%s', $tag->cleanId));
    $link->addClass('tag-link hashtag');
    return $link;
}

function render_tag(Tag $tag)
{
    return new TagView($tag->label, $tag->color);
}

function render_tags(array $tags, $linked = false)
{
    assert_instances_of($tags, 'AnhNhan\Converge\Modules\Tag\Storage\Tag');
    $rendered = [];
    $fun = $linked ? 'link_tag' : 'render_tag';
    foreach (msort($tags, 'displayOrder') as $tag)
    {
        $rendered[] = $fun($tag);
    }
    return $rendered;
}

/**
 * Common use case of rendering multiple tags separated by a glue.
 */
function implode_link_tag($glue, array $tags, $linked = false)
{
    assert_instances_of($tags, 'AnhNhan\Converge\Modules\Tag\Storage\Tag');
    return implode_safeHtml($glue, render_tags($tags, $linked));
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

/**
 * @return true   if successful
 *         string if encountered an error, with the contents being the error message
 *                note that due to PHP's truth semantics, strings are regarded as true, too
 */
function validate_tags_from_form_input($_tags, $tag_query_or_app)
{
    // Parse tags
    if (preg_match('/^\\[.*\\]$/', $_tags)) {
        // It's a Json array
        $tags = json_decode($_tags);

        // Validate JSON structure
        try {
            foreach ($tags as $t) {
                assert_stringlike($t);
                if (empty($t)) {
                    return "Somehow you could sneak up an empty tag?...";
                }
            }
        } catch (\InvalidArgumentException $e) {
            // <ignore>
            return "Invalid tag structure";
        }
    } else {
        // A, B, C
        $tags = explode(',', $_tags);
        $tags = array_map('trim', $tags);
    }

    // People might add existing tags - just ignore such changes, they add unnecessary noise to error messages
    $tags = array_filter($tags);
    $tags = array_unique($tags);

    // Save ourselves the workload
    if (!$tags) {
        return [];
    }

    // Load tags
    $tag_query  = new TagQuery($tag_query_or_app);
    $tagObjects = $tag_query->retrieveTagsForLabels($tags);

    // Validate tags
    // Far-future TODO: Put suggestions there?
    if (count($tagObjects) != count($tags)) {
        $tabOjectLabels = mpull($tagObjects, "label");
        $diffTags = array_diff($tags, $tabOjectLabels);
        return sprintf("The following tags are invalid: '%s'", implode("', '", $diffTags));
    }

    return $tagObjects;
}
