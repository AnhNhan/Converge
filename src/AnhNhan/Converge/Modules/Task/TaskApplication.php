<?php
namespace AnhNhan\Converge\Modules\Task;

use AnhNhan\Converge\Events\ArrayDataEvent;
use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return 'Task';
    }

    public function getInternalName()
    {
        return 'task';
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . '/resources/routes.yml');
    }

    public function getCustomMarkupRules()
    {
        return [
            new Markup\TaskEntity($this),
        ];
    }

    public function getRegisteredEventListeners()
    {
        $recorder = new Activity\TaskRecorder($this->getExternalApplication('activity'));

        return [
            [
                'event.name' => \Event_TaskTransaction_Record,
                'event.listener' => function (ArrayDataEvent $event, $event_name, $dispatcher) use ($recorder)
                {
                    if (!$event->check_array_object_type('AnhNhan\Converge\Modules\Task\Storage\TaskTransaction'))
                    {
                        throw new \InvalidArgumentException('Received invalid object array.');
                    }

                    $recorder->record($event->data);
                },
            ],
        ];
    }

    public function getActivityRenderers()
    {
        return [
            'TASK' => $this->createActivityRenderer('task_activity_label', 'task_activity_body', 'task_activity_class', 'task_activity_external_uids'),
        ];
    }

    public function routeToController(Request $request)
    {
        $routeName = $request->attributes->get('route-name');

        switch ($routeName) {
            case 'task-listing':
                return new Controllers\TaskListing($this);
                break;
            case 'task-create':
            case 'task-edit':
                return new Controllers\TaskEdit($this);
                break;
            case 'task-comment':
                return new Controllers\TaskComment($this);
                break;
            case 'task-close':
                return new Controllers\TaskClose($this);
                break;
            case 'task-rel-assoc':
                return new Controllers\TaskAssoc($this);
                break;
            case 'task-rel-deassoc':
                return new Controllers\TaskAssoc($this);
                break;
            case 'task-display':
                return new Controllers\TaskDisplay($this);
                break;
        }

        return null;
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . '/Storage'));
    }
}
