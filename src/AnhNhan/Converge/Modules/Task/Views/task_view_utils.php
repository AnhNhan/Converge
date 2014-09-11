<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupEngine;
use AnhNhan\Converge\Modules\Task\Storage\Task;
use AnhNhan\Converge\Modules\Task\Storage\TaskStatus;
use AnhNhan\Converge\Modules\Task\Storage\TaskPriority;
use AnhNhan\Converge\Modules\Task\Storage\TaskTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Views\Form\Controls\HiddenControl;
use AnhNhan\Converge\Views\Objects\Listing;
use AnhNhan\Converge\Views\Objects\Object;
use AnhNhan\Converge\Views\Property\PropertyList;

use Diff as DiffEngine;
use AnhNhan\Converge\Modules\Markup\Diff\Renderer\InText as InlineDiffRenderer;

function render_task_listing(array $tasks, $title = null, $empty_message = 'No tasks available', callable $object_adder = null)
{
    $object_adder = $object_adder ?: 'task_listing_add_object';
    $listing = new Listing;
    $listing->setTitle($title);
    $listing->setEmptyMessage($empty_message);
    foreach ($tasks as $task) {
        $object_adder($listing, $task);
    }

    return $listing;
}

function task_listing_basic_object(Task $task)
{
    $object = new Object;
    $object
        ->setId($task->uid)
        ->setHeadline($task->label)
        ->setHeadHref("/task/" . $task->label_canonical)
    ;

    if ($task->closed)
    {
        $object->addAttribute(cv\icon_ion('closed', 'checkmark', false));
        $object->addClass('task-object-closed');
    }
    else
    {
        $object->addAttribute($task->status->label);
    }
    $task->tags->count() and $object->addAttribute(implode_link_tag(' ', mpull($task->tags->toArray(), 'tag'), true));

    return $object;
}

function task_listing_add_object(Listing $listing, Task $task)
{
    $object = task_listing_basic_object($task);

    $object->addDetail($task->modifiedAt->format("D, d M 'y"), 'calendar');
    if ($task->assigned)
    {
        $object->addDetail(strong(implode_link_user(', ', mpull($task->assigned, 'user'))), 'person-stalker');
    }
    else
    {
        $object->addDetail(span('muted', 'not assigned'));
    }

    $listing->addObject($object);
}

function render_task_assoc_picker_listing($return_uri, Task $base_task, array $tasks, $title = null, $empty_message = 'No tasks available')
{
    return render_task_listing(
            $tasks,
            $title,
            $empty_message,
            curry_fa('task_assoc_picker_add_object', $return_uri, $base_task)
        )
        ->addClass('task-assoc-picker-listing')
    ;
}

function task_assoc_picker_add_object($return_uri, Task $base_task, Listing $listing, Task $task)
{
    $object = task_listing_basic_object($task)
        ->addClass('task-assoc-picker-object')
        ->addDetail(
            form('', urisprintf('task/assoc/%p?return_to=%s', $base_task->label_canonical, $return_uri), 'POST')
                ->addClass('btn btn-small btn-entity btn-form btn-task-rel')
                ->append(form_hidden('parent_uid', $base_task->uid))
                ->append(form_hidden('child_uid', $task->uid))
                ->append(form_hidden('type', 'taskblocker'))
                ->append(form_hidden('action', 'assoc'))
                ->append(cv\ht('button', cv\icon_ion('add as task blocker', 'android-hand'))
                    ->addClass('btn btn-small btn-entity btn-task-rel')
                    ->addOption('name', '__submit__')
                )
        )
        ->addDetail(
            form('', urisprintf('task/assoc/%p?return_to=%s', $base_task->label_canonical, $return_uri), 'POST')
                ->addClass('btn btn-small btn-entity btn-form btn-task-rel')
                ->append(form_hidden('parent_uid', $base_task->uid))
                ->append(form_hidden('child_uid', $task->uid))
                ->append(form_hidden('type', 'tasksubtask'))
                ->append(form_hidden('action', 'assoc'))
                ->append(cv\ht('button', cv\icon_ion('add as sub task', 'fork-repo'))
                    ->addClass('btn btn-small btn-entity btn-task-rel')
                    ->addOption('name', '__submit__')
                )
        )
    ;

    $listing->addObject($object);
}

function render_task(Task $task, $authenticated, $full_view = true)
{
    $closed_msg = $task->closed
        ? cv\icon_ion('closed', 'checkmark', false)
        : $task->status->label
    ;
    $header_text = cv\hsprintf('<h2>%s <small>%s</small></h2>', $task->label, $closed_msg);
    $panel = panel($header_text, 'task-panel');
    $panel->setId($task->uid);

    $edit_button = cv\ht('a', cv\icon_ion('edit task', 'edit'))
        ->addClass('btn btn-primary btn-small')
        ->addOption('href', urisprintf('task/edit/%p', $task->label_canonical))
    ;
    $close_button_label = $task->closed ? 'open task' : 'close task';
    $close_button_icon  = $task->closed ? 'archive' : 'checkmark';
    $close_button = form('', urisprintf('task/close/%p', $task->label_canonical), 'POST')
        ->addClass('btn btn-form btn-default btn-small')
        ->append(form_hidden('closed', sprintf('%d', !$task->closed)))
        ->append(cv\ht('button', cv\icon_ion($close_button_label, $close_button_icon))
            ->addOption('name', '__submit__')
        )
    ;
    $subtask_button = a(cv\icon_ion('create subtask', 'fork-repo'), urisprintf('task/create?parent_task_id=%s', $task->label_canonical))
        ->addClass('btn btn-default btn-small')
    ;
    $assoc_button = a(cv\icon_ion(cv\icon_ion('add associations', 'android-share'), 'android-add'), urisprintf('task/?parent_task=%s', $task->label_canonical))
        ->addClass('btn btn-default btn-small')
        ->addOption('target', '_blank')
    ;
    $button_container = div('task-panel-buttons pull-right')
        ->append($edit_button)
        ->append($close_button)
        ->append($subtask_button)
        ->append($assoc_button)
    ;

    $midriff = $panel->midriff();
    $mid_container = new PropertyList;
    $authenticated and $midriff->push($button_container);
    $midriff->push($mid_container);
    $midriff->push(cv\ht('span')->addClass('clearfix'));

    $assigned_linked = strong(implode_link_user(', ', mpull($task->assigned, 'user')));

    $embed_code = $task->label_canonical == to_canonical($task->label) ? to_slug($task->label) : $task->label_canonical;

    $mid_container
        ->addEntry('Priority', $task->priority->label)
        ->addEntry('Assigned to', $task->assigned ? $assigned_linked : span('muted', 'up for grabs'))
        ->addEntry('Tags', $task->tags->count() ? implode_link_tag(' ', mpull($task->tags->toArray(), 'tag'), true) : span('muted', 'no tags'))
        ->addEntry('Embed Code', cv\ht('code', '~' . $embed_code))
    ;

    if (!$full_view)
    {
        return $panel;
    }

    $relation_map = [
        [
            'access' => 'blockedBy',
            'task'   => 'blockingTask',
            'label'  => 'Blocked by',
            'type'   => 'taskblocker',
        ],
        [
            'access' => 'subTasks',
            'task'   => 'subTask',
            'label'  => 'Sub tasks',
            'type'   => 'tasksubtask',
        ],
        [
            'access' => 'blockedTasks',
            'task'   => 'parentTask',
            'label'  => 'Blocking',
            'type'   => 'taskblocker',
        ],
        [
            'access' => 'parentTasks',
            'task'   => 'parentTask',
            'label'  => 'Parent tasks',
            'type'   => 'tasksubtask',
        ],
    ];

    foreach ($relation_map as $_)
    {
        $_access = $_['access'];
        $task_property = $_['task'];
        $_label = $_['label'];
        $type = $_['type'];
        if ($task->$_access->count())
        {
            $tasks = mpull($task->$_access->toArray(), $task_property);
            $sorted = msort($tasks, 'closed');
            $blockers = pull($sorted, function ($a_task) use ($task, $type, $task_property, $authenticated)
            {
                $contents = phutil_utf8_shorten($a_task->label, 40);
                $is_shortened = $contents != $a_task->label;

                if ($a_task->closed)
                {
                    $contents = cv\ht('del', $contents);
                }

                $group = span('btn-group');
                if ($is_shortened)
                {
                    $group
                        ->addOption("data-toggle", "tooltip")
                        ->addOption("title", $a_task->label)
                    ;
                }

                $link = a($contents, 'task/' . $a_task->label_canonical)
                    ->addClass('btn btn-entity btn-small btn-task-rel')
                ;
                $group->append($link);

                if ($authenticated)
                {
                    $parent_task = $task_property == 'parentTask' ? $a_task : $task;
                    $child_task  = $task_property == 'parentTask' ? $task : $a_task;
                    $group
                        ->append(
                            form('', urisprintf('task/delete_assoc/%p', $task->label_canonical), 'POST')
                                ->addClass('btn btn-small btn-entity btn-form btn-task-rel')
                                ->append(form_hidden('parent_uid', $parent_task->uid))
                                ->append(form_hidden('child_uid', $child_task->uid))
                                ->append(form_hidden('type', $type))
                                ->append(form_hidden('action', 'deassoc'))
                                ->append(cv\ht('button', cv\icon_ion('', 'close-circled'))
                                    ->addClass('btn btn-small btn-entity btn-task-rel')
                                    ->addOption('name', '__submit__')
                                )
                        )
                    ;
                }

                return $group;
            });
            $mid_container->addEntry($_label, implode_safeHtml(' ', array_map('strong', $blockers)));
        }
    }

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
    $panel->setId($xact->uid);

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
    $tags = idx($other, 'tags', []);
    $users = idx($other, 'users', []);
    $statuses = idx($other, 'status', []);
    $priorities = idx($other, 'priorities', []);

    switch ($xact->type)
    {
        case TransactionEntity::TYPE_CREATE:
            return 'created this task';
        case TaskTransaction::TYPE_EDIT_LABEL:
            return cv\hsprintf('renamed task from %s to %s', strong($xact->oldValue), strong($xact->newValue));
        case TaskTransaction::TYPE_EDIT_DESC:
            $old_text = explode("\n", $xact->oldValue);
            $new_text = explode("\n", $xact->newValue);
            $add_lines = array_diff($new_text, $old_text);
            $del_lines = array_diff($old_text, $new_text);
            $add_count = count($add_lines);
            $del_count = count($del_lines);
            $actual_additions = array_diff(array_keys($add_lines), array_keys($del_lines));
            $actual_deletions = array_diff(array_keys($del_lines), array_keys($add_lines));
            $actual_add_count = count($actual_additions);
            $actual_del_count = count($actual_deletions);
            $replaced_count = min($add_count - $actual_add_count, $del_count - $actual_del_count);
            return cv\hsprintf(
                'changed the description (<strong class="color-fg-belize-hole">%d+</strong> <strong class="color-fg-orange">%d-</strong> <strong class="color-fg-wisteria">%d~</strong>) %s',
                $actual_add_count,
                $actual_del_count,
                $replaced_count,
                span('show-diff-link btn btn-small btn-default', '(diff)')
                    ->addOption('data-xact-panel-id', $xact->uid)
            );
        case TaskTransaction::TYPE_ADD_COMMENT:
            return 'added a comment';
        case TaskTransaction::TYPE_EDIT_CLOSED:
            if ($xact->newValue)
            {
                return 'closed this task';
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
        case TaskTransaction::TYPE_ADD_TAG:
            return cv\hsprintf('added tag <strong>%s</strong>', link_tag(idx($tags, $xact->newValue)));
        case TaskTransaction::TYPE_DEL_TAG:
            return cv\hsprintf('removed tag <strong>%s</strong>', link_tag(idx($tags, $xact->oldValue)));
        case TaskTransaction::TYPE_ADD_RELATION:
        case TaskTransaction::TYPE_DEL_RELATION:
            $val = (array) ($xact->newValue ? json_decode($xact->newValue) : json_decode($xact->oldValue));
            return task_xact_relation_label($val, $xact->type == TaskTransaction::TYPE_ADD_RELATION, $task, $other);
        default:
            return 'did something';
    }
}

function task_xact_relation_label($val, $is_add, Task $task, array $other = [])
{
    $tasks = $other['tasks'];
    $parent_task = $tasks[$val['parent']];
    $child_task = $tasks[$val['child']];

    $is_parent = $val['parent'] == $task->uid;
    $other_task = $is_parent ? $child_task : $parent_task;
    $other_link = a(phutil_utf8_shorten($other_task->label, 40), 'task/' . $other_task->label_canonical);
    $action = $is_add ? 'added' : 'removed';
    if ($val['type'] == 'taskblocker')
    {
        $type = $is_parent ? 'blocking task' : 'blocked task';
    }
    else
    {
        $type = $is_parent ? 'sub task' : 'parent task';
    }

    return cv\hsprintf('%s %s <strong>%s</strong>', $action, $type, $other_link);
}

function task_xact_type_body(Task $task, TaskTransaction $xact, $other = null)
{
    switch ($xact->type)
    {
        case TaskTransaction::TYPE_EDIT_DESC:
            $oldValue = diff\utils\save_html(MarkupEngine::fastParse($xact->oldValue, idx($other, 'markup_rules', [])));
            $newValue = diff\utils\save_html(MarkupEngine::fastParse($xact->newValue, idx($other, 'markup_rules', [])));
            $diff = new DiffEngine(explode("\n", $oldValue), explode("\n", $newValue), []);
            $body = diff\utils\restore_html($diff->render(new InlineDiffRenderer));
            return cv\safeHtml($body);
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
        case TaskTransaction::TYPE_ADD_TAG:
            return [$xact->newValue];
        case TaskTransaction::TYPE_DEL_ASSIGN:
        case TaskTransaction::TYPE_DEL_TAG:
            return [$xact->oldValue];
        case TaskTransaction::TYPE_EDIT_STATUS:
        case TaskTransaction::TYPE_EDIT_PRIORITY:
            return [$xact->oldValue, $xact->newValue];
        case TaskTransaction::TYPE_ADD_RELATION:
        case TaskTransaction::TYPE_DEL_ASSIGN:
            $val = (array) ($xact->newValue ? json_decode($xact->newValue) : json_decode($xact->oldValue));
            return array_values(array_select_keys($val, ['parent', 'child']));
    }
}

// Activity

use AnhNhan\Converge\Modules\Activity\Storage\RecordedActivity;

function task_activity_label(RecordedActivity $activity, array $other = [])
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
        case TaskTransaction::TYPE_EDIT_DESC:
            return cv\hsprintf('clarified the description of task %s', $object_label_link);
        case TaskTransaction::TYPE_EDIT_LABEL:
            $new_label_link = strong(a(phutil_utf8_shorten($activity->xact_contents, 40), $activity->object_link));
            return cv\hsprintf('renamed task %s to %s', $object_label_link, $new_label_link);
        case TaskTransaction::TYPE_ADD_COMMENT:
            return cv\hsprintf('commented on task %s', $object_label_link);
        case TaskTransaction::TYPE_EDIT_CLOSED:
            if ($activity->xact_contents)
            {
                return cv\hsprintf('closed task %s', $object_label_link);
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

function task_activity_body(RecordedActivity $activity, array $other = [])
{
    switch ($activity->xact_type)
    {
        case TaskTransaction::TYPE_ADD_COMMENT:
            return cv\safeHtml(MarkupEngine::fastParse(phutil_utf8_shorten($activity->xact_contents, 160), idx($other, 'markup_rules', [])));
        default:
            return null;
    }
}

function task_activity_class(RecordedActivity $activity, array $other = [])
{
    switch ($activity->xact_type)
    {
        case TransactionEntity::TYPE_CREATE:
        case TaskTransaction::TYPE_EDIT_DESC:
        case TaskTransaction::TYPE_ADD_COMMENT:
            return 'activity-content';
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
