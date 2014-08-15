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

    public function routeToController(Request $request)
    {
        $routeName = $request->attributes->get("route-name");

        switch ($routeName) {
            case "main-listing":
                return new Controllers\DiscussionListingController($this);
                break;
            case "disq-creation":
                return new Controllers\DiscussionEditController($this);
                break;
            case "disq-display":
                return new Controllers\DiscussionDisplayController($this);
                break;
            case "disq-posting":
                return new Controllers\PostEditController($this);
                break;
        }

        return null;
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . "/Storage"));
    }
}
