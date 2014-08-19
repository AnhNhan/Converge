<?php
namespace AnhNhan\Converge\Modules\Task\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskListing extends AbstractTaskController
{
    public function handle()
    {
        $query = $this->buildQuery();
        $tasks = $query->retrieveTasks(null);
        $user_query = create_user_query($this->externalApp('user'));
        fetch_external_authors($tasks, $user_query, 'assignedId', 'setAssigned', 'assigned');

        $container = new MarkupContainer;

        $container->push(h1('Task Listing'));

        $priorities = mpull($tasks, 'priority');
        $priorities = mkey($priorities, 'label');
        $priorities = msort($priorities, 'displayOrder');

        $task_groups = group($tasks, function ($task) { return $task->priority->label; });
        $sorted_task_groups = array_select_keys($task_groups, array_keys($priorities));

        foreach ($sorted_task_groups as $priority_label => $task_group)
        {
            $listing = render_task_listing($task_group, $priority_label);
            $container->push($listing);
        }

        $container->push(cv\safeHtml('<style>.objects-list-container{margin-top: 0;}</style>'));

        // Add link to create new task
        $container->unshift(cv\ht("a", "create new task", array(
            "href"  => "/task/create",
            "class" => "btn btn-primary",
            "style" => "float: right;",
        )));

        $payload = new HtmlPayload;
        $payload->setTitle('Task Listing');
        $payload->setPayloadContents($container);
        return $payload;
    }
}
