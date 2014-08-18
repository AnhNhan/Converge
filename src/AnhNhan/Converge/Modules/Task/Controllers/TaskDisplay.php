<?php
namespace AnhNhan\Converge\Modules\Task\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupEngine;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskDisplay extends AbstractTaskController
{
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
        fetch_external_authors([$task], $user_query);
        fetch_external_authors([$task], $user_query, 'assignedId', 'setAssigned', 'assigned');

        $container = new MarkupContainer;
        $task_panel = render_task($task);
        $container->push($task_panel);

        if ($task->description)
        {
            $custom_rules = get_custom_markup_rules($this->app->getService('app.list'));
            $desc = MarkupEngine::fastParse($task->description, $custom_rules);
            $task_panel->append(cv\safeHtml($desc));
        }

        $payload = new HtmlPayload;
        $payload->setTitle('Task ' . $task->label);
        $payload->setPayloadContents($container);
        return $payload;
    }
}
