<?php
namespace AnhNhan\Converge\Modules\Activity;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ActivityApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Activity";
    }

    public function getInternalName()
    {
        return "activity";
    }

    public function getCustomMarkupRules()
    {
        return [
        ];
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . "/resources/routes.yml");
    }

    public function routeToController(Request $request)
    {
        $routeName = $request->attributes->get("route-name");
        switch ($routeName) {
            case "activity-main":
                return new Controllers\ActivityListing($this);
                break;
        }
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . "/Storage"));
    }
}
