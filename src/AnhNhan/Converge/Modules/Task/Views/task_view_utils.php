<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupEngine;
use AnhNhan\Converge\Modules\Task\Storage\Task;
use AnhNhan\Converge\Modules\Task\Storage\TaskStatus;
use AnhNhan\Converge\Modules\Task\Storage\TaskPriority;
use AnhNhan\Converge\Modules\Task\Storage\TaskTransaction;
use AnhNhan\Converge\Views\Objects\Listing;
use AnhNhan\Converge\Views\Objects\Object;
use AnhNhan\Converge\Views\Property\PropertyList;

use Diff as DiffEngine;
use AnhNhan\Converge\Modules\Markup\Diff\Renderer\Inline as InlineDiffRenderer;

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
        ->setHeadHref("/task/" . $task->label_canonical)
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

function render_task(Task $task)
{
    $panel = panel(h2($task->label), 'task-panel');

    $edit_button = cv\ht('a', cv\icon_ion('edit task', 'edit'))
        ->addClass('btn btn-primary btn-small')
        ->addOption('href', urisprintf('task/%p/edit', $task->label_canonical))
    ;
    $complete_button = cv\ht('a', cv\icon_ion('complete', 'checkmark'))
        ->addClass('btn btn-default btn-small')
        ->addOption('href', urisprintf('task/%p/complete', $task->label_canonical))
    ;
    $button_container = div('task-panel-buttons pull-right')
        ->append($edit_button)
        ->append($complete_button)
    ;

    $midriff = $panel->midriff();
    $mid_container = new PropertyList;
    $midriff->push($button_container);
    $midriff->push($mid_container);

    $mid_container
        ->addEntry('Original author', strong(link_user($task->author)))
        ->addEntry('Completed', $task->completed ? 'yes' : 'no')
        ->addEntry('Priority', $task->priority->label)
        ->addEntry('Status', $task->status->label)
        ->addEntry('Assigned to', $task->assigned ? strong(link_user($task->assigned)) : null)
    ;

    return $panel;
}

function render_task_transaction(Task $task, TaskTransaction $xact, $other = null)
{
    $type_label = task_xact_type_label($task, $xact, $other);
    $header = cv\hsprintf(
        '<div class="pull-right">%s</div><img src="%s" class="user-profile-image" /><div><strong>%s</strong> <span class="minor-stuff">%s</span></div>',
        $xact->createdAt->format("D, d M 'y"),
        $xact->actor->getGravatarImagePath,
        link_user($xact->actor),
        $type_label
    );
    $panel = panel($header, 'task-panel-xact');

    $panel_body = task_xact_type_body($task, $xact, $other);
    if ($panel_body)
    {
        $panel->append($panel_body);
    }

    $panel_class = task_xact_type_class($task, $xact, $other);
    if ($panel_class)
    {
        $panel->addClass($panel_class);
    }

    return $panel;
}

function task_xact_type_label(Task $task, TaskTransaction $xact, $other = null)
{
    switch ($xact->type)
    {
        case TaskTransaction::TYPE_EDIT_LABEL:
            return 'changed the label';
        case TaskTransaction::TYPE_EDIT_DESC:
            return 'changed the description';
        case TaskTransaction::TYPE_ADD_COMMENT:
            return 'added a comment';
        case TaskTransaction::TYPE_EDIT_STATUS:
            return cv\hsprintf('changed the status from <em>%s</em> to <em>%s</em>', $xact->oldValue, $xact->newValue);
        case TaskTransaction::TYPE_EDIT_PRIORITY:
            return cv\hsprintf('changed the priority from <em>%s</em> to <em>%s</em>', $xact->oldValue, $xact->newValue);
        case TaskTransaction::TYPE_EDIT_ASSIGN:
            return cv\hsprintf('reassigned the task to <em>%s</em>', $xact->newValue);
        default:
            return 'did something';
    }
}

function task_xact_type_body(Task $task, TaskTransaction $xact, $other = null)
{
    switch ($xact->type)
    {
        case TaskTransaction::TYPE_EDIT_LABEL:
            $diff = new DiffEngine([$xact->oldValue], [$xact->newValue], []);
            return cv\safeHtml($diff->render(new InlineDiffRenderer));
        case TaskTransaction::TYPE_EDIT_DESC:
            $diff = new DiffEngine(explode("\n", $xact->oldValue), explode("\n", $xact->newValue), []);
            return cv\safeHtml($diff->render(new InlineDiffRenderer));
        case TaskTransaction::TYPE_ADD_COMMENT:
            return cv\safeHtml(MarkupEngine::fastParse($xact->newValue, idx($other, 'markup_rules', [])));
        default:
            return null;
    }
}

function task_xact_type_class(Task $task, TaskTransaction $xact, $other = null)
{
    switch ($xact->type)
    {
        case TaskTransaction::TYPE_EDIT_LABEL:
        case TaskTransaction::TYPE_EDIT_DESC:
            return 'task-panel-xact-diff';
        case TaskTransaction::TYPE_ADD_COMMENT:
            return 'task-panel-xact-comment';
        default:
            return null;
    }
}
