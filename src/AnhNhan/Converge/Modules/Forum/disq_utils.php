<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumListing;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\Converge\Modules\Forum\Views\Objects\PaneledForumListing;
use AnhNhan\Converge\Modules\Tag\Views\TagView;

function render_disq_listing(array $disqs, array $postCounts, $title = null)
{
    $listing = new ForumListing;
    $listing->setTitle($title);
    foreach ($disqs as $discussion) {
        $object = new ForumObject;
        $object
            ->setHeadline($discussion->label)
            ->setHeadHref("/disq/" . $discussion->cleanId)
            ->postCount(idx($postCounts, $discussion->uid)["postcount"]);

        $tags = mpull($discussion->tags->toArray(), "tag");
        $tags = msort($tags, "label");
        $tags = array_reverse($tags);
        $tags = msort($tags, "displayOrder");
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $object->addTag(new TagView($tag->label, $tag->color));
            }
        }

        $object->addDetail($discussion->lastActivity->format("D, d M 'y"));
        $object->addDetail(cv\ht('strong', link_user($discussion->author)));

        $listing->addObject($object);
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
        $object = new ForumObject;
        $object
            ->setHeadline($discussion->label)
            ->setHeadHref("/disq/" . $discussion->cleanId)
            ->postCount(idx($postCounts, $discussion->uid)["postcount"]);

        $tags = mpull($discussion->tags->toArray(), "tag");
        $tags = msort($tags, "label");
        $tags = array_reverse($tags);
        $tags = msort($tags, "displayOrder");
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $object->addTag(new TagView($tag->label, $tag->color));
            }
        }

        $object->addDetail($discussion->lastActivity->format("D, d M 'y"));
        $object->addDetail(cv\ht('strong', link_user($discussion->author)));

        $panelForumListing->addObject($object);
    }

    return $panelForumListing;
}
