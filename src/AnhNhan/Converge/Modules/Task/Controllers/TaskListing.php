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
        $request = $this->request;
        $all_tasks_flag = (boolean) $request->get('all_tasks');
        $query = $this->buildQuery();
        if ($all_tasks_flag)
        {
            $tasks = $query->retrieveTasks(null);
        }
        else
        {
            $tasks = $query->retrieveUnclosedTasks(null);
        }

        $parent_task = null;
        if ($parent_task_id = $request->query->get('parent_task'))
        {
            $parent_task = head($query->retrieveTasksForCanonicalLabels([$parent_task_id]));
            if (!$parent_task)
            {
                return (new ResponseHtml404)->setText('Parent task does not exist!');
            }
        }

        $user_query = create_user_query($this->externalApp('user'));
        $assigned_objs = mpull($tasks, 'assigned');
        $parent_task and $assigned_objs[] = $parent_task->assigned;
        $assigned_objs = array_mergev($assigned_objs);
        fetch_external_authors($assigned_objs, $user_query, 'userId', 'setUser', 'user');
        $query->fetchExternalTags(array_merge($tasks, $parent_task ? [$parent_task] : []));

        $container = new MarkupContainer;

        $page_title = $parent_task ? 'Pick associations for ' . $parent_task->label : cv\hsprintf('Task Listing <small>(%d)</small>', count($tasks));

        $container->push(h1($page_title));

        $parent_task and $container->push(render_task($parent_task, false));

        $priorities = mpull($tasks, 'priority');
        $priorities = mkey($priorities, 'label');
        $priorities = msort($priorities, 'displayOrder');

        $task_groups = group($tasks, function ($task) { return $task->priority->label; });
        $sorted_task_groups = array_select_keys($task_groups, array_keys($priorities));

        $render_listing = $parent_task ? curry_fa(curry_fa('render_task_assoc_picker_listing', $request->getRequestUri()), $parent_task) : 'render_task_listing';

        foreach ($sorted_task_groups as $priority_label => $task_group)
        {
            $listing = $render_listing($task_group, cv\hsprintf('%s <small>(%d)</small>', $priority_label, count($task_group)));
            $container->push($listing);
        }

        if (!$sorted_task_groups)
        {
            $container->push(div('objects-list-empty-message', 'No tasks available'));
        }

        $container->push(cv\safeHtml('<style>.objects-list-container{margin-top: 0;}</style>'));

        $button_container = div('pull-right');
        !$parent_task and $container->unshift($button_container);
        $button_container->getContent()->unshift(cv\ht("a", $all_tasks_flag ? 'show open tasks only' : 'show all tasks', array(
            "href"  => "/task/?all_tasks=" . ($all_tasks_flag ? '0' : '1'),
            "class" => "btn btn-default",
        )));
        // Add link to create new task
        $this->isGranted('ROLE_USER') and $button_container->getContent()->unshift(cv\ht("a", "create new task", array(
            "href"  => "/task/create",
            "class" => "btn btn-primary",
        )));

        $this->resMgr
            ->requireCss('application-task-display')
            ->requireCss('application-task-listing')
        ;

        $payload = new HtmlPayload;
        $payload->setTitle($page_title);
        $payload->setPayloadContents($container);
        return $payload;
    }
}
