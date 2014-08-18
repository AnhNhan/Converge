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
        $query = $this->buildQuery();

        $task = $this->retrieveTaskObject($request, $query);
        if (!$task)
        {
            return id(new ResponseHtml404)->setText('This is not the task you are looking for.');
        }
        $user_query = create_user_query($this->externalApp('user'));
        $user_uids = mpull($task->transactions->toArray(), 'actorId');
        $user_uids[] = $task->authorId;
        if ($task->assignedId)
        {
            $user_uids[] = $task->assignedId;
        }
        $user_objects = $user_query->retrieveUsersForUIDs($user_uids);

        $task->setAuthor(idx($user_objects, $task->authorId));
        $task->setAssigned(idx($user_objects, $task->assignedId));

        $container = new MarkupContainer;
        $task_panel = render_task($task);
        if ($task->description)
        {
            $custom_rules = get_custom_markup_rules($this->app->getService('app.list'));
            $desc = MarkupEngine::fastParse($task->description, $custom_rules);
            $task_panel->append(cv\safeHtml($desc));
        }
        $container->push($task_panel);

        if ($task->transactions->count())
        {
            $container->push(h2('Timeline'));
        }

        foreach ($task->transactions as $xact)
        {
            if ($xact->type == TransactionEntity::TYPE_CREATE)
            {
                continue;
            }

            if ($xact->createdAt->getTimestamp() < $task->createdAt->getTimestamp() + self::CreatePeriodGraceTime)
            {
                continue;
            }

            $xact_author = idx($user_objects, $xact->actorId);
            $xact->setAuthor($xact_author);
            $rendered_xact = render_task_transaction($task, $xact);
            $container->push($rendered_xact);
        }

        $this->app->getService('resource_manager')
            ->requireCss('application-diff')
            ->requireCss('application-task-display')
        ;

        $payload = new HtmlPayload;
        $payload->setTitle('Task ' . $task->label);
        $payload->setPayloadContents($container);
        return $payload;
    }
}
