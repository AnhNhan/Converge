<?php
namespace AnhNhan\Converge\Modules\Task;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Task";
    }

    public function getInternalName()
    {
        return "task";
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . "/resources/routes.yml");
    }

    public function getActivityRenderers()
    {
        return [
            'TASK' => $this->createActivityRenderer('task_activity_label', 'task_activity_body'),
        ];
    }

    public function routeToController(Request $request)
    {
        $routeName = $request->attributes->get("route-name");

        switch ($routeName) {
            case "task-listing":
                return new Controllers\TaskListing($this);
                break;
            case "task-create":
            case "task-edit":
                return new Controllers\TaskEdit($this);
                break;
            case "task-comment":
                return new Controllers\TaskComment($this);
                break;
            case "task-complete":
                return new Controllers\TaskComplete($this);
                break;
            case "task-display":
                return new Controllers\TaskDisplay($this);
                break;
        }

        return null;
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . "/Storage"));
    }
}
