<?php
namespace AnhNhan\Converge\Modules\Task\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupEngine;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Modules\Task\Storage\TaskTransaction;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskDisplay extends AbstractTaskController
{
    const CreatePeriodGraceTime = 60;

    public function handle()
    {
        $request = $this->request;
        $user_authenticated = $this->isGranted('ROLE_USER');
        $query = $this->buildQuery();

        $task = head($query->retrieveTasksForCanonicalLabelsWithXacts([$request->get('id')]));
        if (!$task)
        {
            return id(new ResponseHtml404)->setText('This is not the task you are looking for.');
        }
        $non_skippable_types = [
            TransactionEntity::TYPE_CREATE       => true,
            TaskTransaction::TYPE_ADD_COMMENT    => true,
            TaskTransaction::TYPE_EDIT_CLOSED    => true,
            TaskTransaction::TYPE_ADD_RELATION   => true,
            TaskTransaction::TYPE_DEL_RELATION   => true,
        ];
        $transactions = $task->transactions->toArray();
        $transactions = array_filter($transactions, function ($xact) use ($task, $non_skippable_types) {
            return isset($non_skippable_types[$xact->type])
                    || $xact->createdAt->getTimestamp() > $task->createdAt->getTimestamp() + self::CreatePeriodGraceTime;
        });
        $external_uids = array_filter(array_mergev(array_filter(array_map('task_xact_type_fetch_external_uids', $transactions))));
        $grouped_external_uids = group($external_uids, 'uid_get_type');

        $external_tag_uids = idx($grouped_external_uids, 'TTAG', []);
        $external_task_uids = idx($grouped_external_uids, 'TASK', []);
        $external_user_uids = idx($grouped_external_uids, 'USER', []);
        $external_status_uids = idx($grouped_external_uids, 'TASK-STAT', []);
        $external_priority_uids = idx($grouped_external_uids, 'TASK-PRIO', []);

        $external_tasks = $external_task_uids ? mkey($query->retrieveTasksForUids(array_values($external_task_uids)), 'uid') : [];
        $external_status = $external_status_uids ? $query->retrieveTaskStatusForUids($external_status_uids) : [];
        $external_priorities = $external_priority_uids ? $query->retrieveTaskPriorityForUids($external_priority_uids) : [];

        $user_query = create_user_query($this->externalApp('user'));
        $user_uids = mpull($transactions, 'actorId');
        $user_uids = array_merge($user_uids, $external_user_uids);
        $user_uids[] = $task->authorId;
        pull($task->assigned, function ($assigned) use (&$user_uids) { $user_uids[] = $assigned->userId; });
        $user_objects = $user_query->retrieveUsersForUIDs($user_uids);

        $task->setAuthor(idx($user_objects, $task->authorId));
        pull($task->assigned, function ($assigned) use ($user_objects) { $assigned->setUser(idx($user_objects, $assigned->userId)); });

        $tag_query = create_tag_query($this->externalApp('tag'));
        $tags = mkey($tag_query->retrieveTagsForIDs(array_merge(mpull($task->tags->toArray(), 'tagId'), $external_tag_uids)), 'uid');
        pull($task->tags, function ($assoc_tag) use ($tags) { $assoc_tag->setTag(idx($tags, $assoc_tag->tagId)); });

        $custom_rules = get_custom_markup_rules($this->app->getService('app.list'));

        $container = new MarkupContainer;
        $task_panel = render_task($task, $user_authenticated);
        if ($task->description)
        {
            $desc = MarkupEngine::fastParse($task->description, $custom_rules);
            $task_panel->append(cv\safeHtml($desc));
        }
        $container->push($task_panel);

        $container->push(h2('Timeline'));

        $other = [
            'markup_rules' => $custom_rules,
            'priorities'   => $external_priorities,
            'status'       => $external_status,
            'users'        => $user_objects,
            'tasks'        => $external_tasks,
            'tags'         => $tags,
        ];

        foreach ($transactions as $xact)
        {
            $xact_author = idx($user_objects, $xact->actorId);
            $xact->setAuthor($xact_author);
            $rendered_xact = render_task_transaction($task, $xact, $other);
            $container->push($rendered_xact);
        }

        $comment_grid = grid();
        $comment_row  = $comment_grid->row();
        $comment_form = form('', urisprintf('task/comment/%s', $task->label_canonical), 'POST')
            ->setDualColumnMode(false)
            ->append($comment_grid)
        ;
        $comment_row->column(6)
            ->push(
                form_textareacontrol(h2('Comment'), 'comment')
                    ->addClass('forum-markup-processing-form')
            )
            ->push(
                form_submitcontrol(null)
            )
        ;
        $comment_row->column(6)
            ->push(h2('Preview'))
            ->push(cv\ht('div', 'Foo')->addClass('markup-preview-output'))
        ;
        if ($user_authenticated)
        {
            $container->push($comment_form);
        }
        else
        {
            $container->push(h2('Comment'));
            $container->push(div('objects-list-empty-message', 'You have to be logged in to comment.'));
        }

        $container->push(cv\ht('script', cv\safeHtml('
$(".show-diff-link").click(_.debounce(function (e) {
    $panel_body = $("#" + $(this).attr("data-xact-panel-id") + ".task-panel-xact-diff .panel-body");
    $panel_body.fadeToggle();
}, 300, true));
        ')));

        $this->app->getService('resource_manager')
            ->requireCss('application-diff')
            ->requireCss('application-task-display')
            ->requireJs('application-forum-markup-preview')
        ;

        $payload = new HtmlPayload;
        $payload->setTitle('Task ' . $task->label);
        $payload->setPayloadContents($container);
        return $payload;
    }
}
