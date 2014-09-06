<?php
namespace AnhNhan\Converge\Modules\Task\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupRule;
use AnhNhan\Converge\Modules\Task\TaskApplication;
use AnhNhan\Converge\Modules\Task\Query\TaskQuery;
use AnhNhan\Converge\Modules\Task\Storage\Task;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskEntity extends MarkupRule
{
    // We allow underscores, dashes and dots as characters for readability.
    // These get filtered out during application.
    const Regex = '/(?<!\w|~)~([\w-_.]+[\w])/';

    private $query;

    public function __construct(TaskApplication $app)
    {
        $this->query = new TaskQuery($app);
    }

    public function apply($text)
    {
        return preg_replace_callback(
            self::Regex,
            [$this, 'applyOccurence'],
            $text
        );
    }

    public function applyOccurence($matches)
    {
        $task_name = to_canonical($matches[0]);
        $task     = head($this->query->retrieveTasksForCanonicalLabels([$task_name]));

        if (!$task)
        {
            // Reusing bad-username class so I won't have to write a new one
            return tooltip('span', $matches[0], 'task not found')->addClass('bad-username');
        }

        return $this->link_task($task);
    }

    private function link_task(Task $task)
    {
        $contents = $task->label;
        $classes = ['task-entity'];
        if ($task->closed)
        {
            $contents = cv\hsprintf('Task %s- %s', cv\icon_ion('', 'checkmark'), $contents);
            $classes[] = 'task-entity-closed';
        }
        else
        {
            $contents = 'Task - ' . $contents;
        }
        return a($contents, 'task/' . $task->label_canonical)->addClass($classes);
    }
}
