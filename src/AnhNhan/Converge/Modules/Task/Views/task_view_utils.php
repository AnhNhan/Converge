<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupEngine;
use AnhNhan\Converge\Modules\Task\Storage\Task;
use AnhNhan\Converge\Modules\Task\Storage\TaskStatus;
use AnhNhan\Converge\Modules\Task\Storage\TaskPriority;
use AnhNhan\Converge\Modules\Task\Storage\TaskTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
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

    if ($task->completed)
    {
        $object->addAttribute(cv\icon_ion('completed', 'checkmark', false));
        $object->addClass('task-object-completed');
    }
    else
    {
        $object->addAttribute($task->status->label);
    }

    $object->addDetail($task->modifiedAt->format("D, d M 'y"));
    if ($task->assigned)
    {
        $assigned_linked = array_map('phutil_safe_html', (array_map('link_user', mpull($task->assigned, 'user'))));
        if ($assigned_linked)
        {
            $object->addDetail(cv\ht('strong', cv\safeHtml(phutil_implode_html(', ', $assigned_linked))));
        }
    }
    else
    {
        $object->addDetail(span('muted', 'not assigned'));
    }

    $listing->addObject($object);
}

function render_task(Task $task)
{
    $completed_msg = $task->completed
        ? cv\icon_ion('completed', 'checkmark', false)
        : $task->status->label
    ;
    $header_text = cv\hsprintf('<h2>%s <small>%s</small></h2>', $task->label, $completed_msg);
    $panel = panel($header_text, 'task-panel');

    $edit_button = cv\ht('a', cv\icon_ion('edit task', 'edit'))
        ->addClass('btn btn-primary btn-small')
        ->addOption('href', urisprintf('task/edit/%p', $task->label_canonical))
    ;
    $complete_button_label = $task->completed ? 'mark as incomplete' : 'mark as complete';
    $complete_button_icon  = $task->completed ? 'archive' : 'checkmark';
    $complete_button = cv\ht('a', cv\icon_ion($complete_button_label, $complete_button_icon))
        ->addClass('btn btn-default btn-small')
        ->addOption('href', urisprintf('task/complete/%p?completed=%b', $task->label_canonical, !$task->completed))
    ;
    $button_container = div('task-panel-buttons pull-right')
        ->append($edit_button)
        ->append($complete_button)
    ;

    $midriff = $panel->midriff();
    $mid_container = new PropertyList;
    $midriff->push($button_container);
    $midriff->push($mid_container);

    $assigned_linked = array_map('phutil_safe_html', array_map('strong', array_map('link_user', mpull($task->assigned, 'user'))));

    $mid_container
        ->addEntry('Priority', $task->priority->label)
        ->addEntry('Assigned to', $assigned_linked ? cv\safeHtml(phutil_implode_html(', ', $assigned_linked)) : span('muted', 'up for grabs'))
        ->addEntry('Last activity', $task->modifiedAt->format("D, d M 'y"))
    ;

    return $panel;
}

function render_task_transaction(Task $task, TaskTransaction $xact, array $other = [])
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

function task_xact_type_label(Task $task, TaskTransaction $xact, array $other = [])
{
    $users = idx($other, 'users', []);
    $statuses = idx($other, 'status', []);
    $priorities = idx($other, 'priorities', []);

    switch ($xact->type)
    {
        case TransactionEntity::TYPE_CREATE:
            return 'created this task';
        case TaskTransaction::TYPE_EDIT_LABEL:
            return 'changed the label';
        case TaskTransaction::TYPE_EDIT_DESC:
            return 'changed the description';
        case TaskTransaction::TYPE_ADD_COMMENT:
            return 'added a comment';
        case TaskTransaction::TYPE_EDIT_COMPLETED:
            if ($xact->newValue)
            {
                return 'completed this task';
            }
            else
            {
                return 'revived this task';
            }
        case TaskTransaction::TYPE_EDIT_STATUS:
            return cv\hsprintf('changed the status from <em>%s</em> to <em>%s</em>', $statuses[$xact->oldValue]->label, $statuses[$xact->newValue]->label);
        case TaskTransaction::TYPE_EDIT_PRIORITY:
            return cv\hsprintf('changed the priority from <em>%s</em> to <em>%s</em>', $priorities[$xact->oldValue]->label, $priorities[$xact->newValue]->label);
        case TaskTransaction::TYPE_ADD_ASSIGN:
            return cv\hsprintf('added <strong>%s</strong> to the task\'s assignees', link_user(idx($users, $xact->newValue)));
        case TaskTransaction::TYPE_DEL_ASSIGN:
            return cv\hsprintf('removed <strong>%s</strong> from this task\'s assignees', link_user(idx($users, $xact->oldValue)));
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
        case TransactionEntity::TYPE_CREATE:
            return 'task-panel-xact-create';
        case TaskTransaction::TYPE_EDIT_LABEL:
        case TaskTransaction::TYPE_EDIT_DESC:
            return 'task-panel-xact-diff';
        case TaskTransaction::TYPE_ADD_COMMENT:
            return 'task-panel-xact-comment';
        default:
            return null;
    }
}

function task_xact_type_fetch_external_uids(TaskTransaction $xact)
{
    switch ($xact->type)
    {
        case TaskTransaction::TYPE_ADD_ASSIGN:
            return [$xact->newValue];
        case TaskTransaction::TYPE_DEL_ASSIGN:
            return [$xact->oldValue];
        case TaskTransaction::TYPE_EDIT_STATUS:
        case TaskTransaction::TYPE_EDIT_PRIORITY:
            return [$xact->oldValue, $xact->newValue];
    }
}

// Activity

use AnhNhan\Converge\Modules\Activity\Storage\RecordedActivity;

function task_activity_label(RecordedActivity $activity, $other)
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
            return cv\hsprintf('created task %s', $object_label_link);
        case TaskTransaction::TYPE_ADD_COMMENT:
            return cv\hsprintf('commented on task %s', $object_label_link);
        case TaskTransaction::TYPE_EDIT_COMPLETED:
            if ($activity->xact_contents)
            {
                return cv\hsprintf('completed task %s', $object_label_link);
            }
            else
            {
                return cv\hsprintf('revived task %s', $object_label_link);
            }
        case TaskTransaction::TYPE_ADD_ASSIGN:
            return cv\hsprintf('assigned <strong>%s</strong> to task %s', $user_link(), $object_label_link);
        case TaskTransaction::TYPE_DEL_ASSIGN:
            return cv\hsprintf('removed <strong>%s</strong> from task %s\'s assignees', $user_link(), $object_label_link);
        default:
            return 'did something';
    }
}

function task_activity_body(RecordedActivity $activity, $other)
{
    switch ($activity->xact_type)
    {
        case TaskTransaction::TYPE_ADD_COMMENT:
            return cv\safeHtml(MarkupEngine::fastParse(phutil_utf8_shorten($activity->xact_contents, 160), idx($other, 'markup_rules', [])));
        default:
            return null;
    }
}

function task_activity_external_uids(RecordedActivity $activity)
{
    switch ($activity->xact_type)
    {
        case TaskTransaction::TYPE_ADD_ASSIGN:
        case TaskTransaction::TYPE_DEL_ASSIGN:
            return $activity->xact_contents;
        default:
            return null;
    }
}
