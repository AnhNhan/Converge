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
        $all_tasks_flag = (boolean) $this->request->get('all_tasks');
        $query = $this->buildQuery();
        if ($all_tasks_flag)
        {
            $tasks = $query->retrieveTasks(null);
        }
        else
        {
            $tasks = $query->retrieveIncompleteTasks(null);
        }
        $user_query = create_user_query($this->externalApp('user'));
        $assigned_objs = mpull($tasks, 'assigned');
        $assigned_objs = array_mergev($assigned_objs);
        fetch_external_authors($assigned_objs, $user_query, 'userId', 'setUser', 'user');
        $query->fetchExternalTags($tasks);

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

        if (!$sorted_task_groups)
        {
            $container->push(div('objects-list-empty-message', 'No tasks available'));
        }

        $container->push(cv\safeHtml('<style>.objects-list-container{margin-top: 0;}</style>'));

        $button_container = div('pull-right');
        $container->unshift($button_container);
        $button_container->getContent()->unshift(cv\ht("a", $all_tasks_flag ? 'show incomplete only' : 'show all tasks', array(
            "href"  => "/task/?all_tasks=" . ($all_tasks_flag ? '0' : '1'),
            "class" => "btn btn-default",
        )));
        // Add link to create new task
        $this->isGranted('ROLE_USER') and $button_container->getContent()->unshift(cv\ht("a", "create new task", array(
            "href"  => "/task/create",
            "class" => "btn btn-primary",
        )));

        $this->resMgr
            ->requireCss('application-task-listing')
        ;

        $payload = new HtmlPayload;
        $payload->setTitle('Task Listing');
        $payload->setPayloadContents($container);
        return $payload;
    }
}
