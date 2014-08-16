<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Task\Storage\Task;
use AnhNhan\Converge\Modules\Task\Storage\TaskStatus;
use AnhNhan\Converge\Modules\Task\Storage\TaskPriority;
use AnhNhan\Converge\Views\Objects\Listing;
use AnhNhan\Converge\Views\Objects\Object;

function render_task_listing(array $tasks, $title = null, $empty_message = 'No tasks available')
{
    $listing = new Listing;
    $listing->setTitle($title);
    $listing->setEmptyMessage($empty_message);
    foreach ($tasks as $task) {
        task_listing_add_object($listing, $task);
    }

    return $listing;
}

function task_listing_add_object(Listing $listing, Task $task)
{
    $object = new Object;
    $object
        ->setHeadline($task->label)
        ->setHeadHref("/task/" . $task->cleanId)
    ;

    $object->addAttribute($task->priority->label);
    $object->addAttribute($task->status->label);
    $object->addAttribute(cv\hsprintf('created by <strong>%s</strong>', link_user($task->author)));

    $object->addDetail($task->modifiedAt->format("D, d M 'y"));
    if ($task->assigned)
    {
        $object->addDetail(cv\ht('strong', link_user($task->assigned)));
    }

    $listing->addObject($object);
}

