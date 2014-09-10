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
        $original = $matches[0];
        $taskname = to_canonical($original);
        $metadata = [
            'original' => $original,
            'taskname' => $taskname,
        ];
        $token = $this->storage->store($original);
        $this->storage->addTokenToSet('task-mention', $token, $metadata);
        return $token;
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

    public function didMarkupText()
    {
        $token_set = $this->storage->getTokenSet('task-mention');
        if (!$token_set)
        {
            return;
        }

        $task_names = ipull($token_set, 'taskname');
        $tasks      = $this->query->retrieveTasksForCanonicalLabels(array_values($task_names));
        $tasks      = mkey($tasks, 'label_canonical');
        $tasks      = array_map([$this, 'link_task'], $tasks);
        foreach ($token_set as $token => $metadata)
        {
            $this->storage->overwrite($token, idx($tasks, $metadata['taskname'], tooltip('span', $metadata['original'], 'task not found')->addClass('bad-username')));
        }
    }
}
