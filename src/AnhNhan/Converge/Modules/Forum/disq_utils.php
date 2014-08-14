<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Forum\Storage\Discussion;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumListing;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\Converge\Modules\Forum\Views\Objects\PaneledForumListing;
use AnhNhan\Converge\Modules\Tag\Views\TagView;

function render_disq_listing(array $disqs, array $postCounts, $title = null)
{
    $listing = new ForumListing;
    $listing->setTitle($title);
    foreach ($disqs as $discussion) {
        disq_listing_add_object($listing, $discussion, idx($postCounts, $discussion->uid)["postcount"]);
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
        disq_listing_add_object($panelForumListing, $discussion, idx($postCounts, $discussion->uid)["postcount"]);
    }

    return $panelForumListing;
}

function disq_listing_add_object(ForumListing $listing, Discussion $discussion, $postCount)
{
    $object = new ForumObject;
    $object
        ->setHeadline($discussion->label)
        ->setHeadHref("/disq/" . $discussion->cleanId)
        ->postCount($postCount);

    $tags = mpull($discussion->tags->toArray(), "tag");
    $tags = msort($tags, "label");
    $tags = array_reverse($tags);
    $tags = msort($tags, "displayOrder");
    foreach ($tags as $tag) {
        if (!empty($tag)) {
            $object->addTagObject($tag);
        }
    }

    $object->addDetail($discussion->lastActivity->format("D, d M 'y"));
    $object->addDetail(cv\ht('strong', link_user($discussion->author)));

    $listing->addObject($object);
}
