<?php
namespace AnhNhan\Converge\Modules\StaticResources;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class StaticResourcesApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Static Resources";
    }

    public function getInternalName()
    {
        return "static-rsrcs";
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . "/resources/routes.yml");
    }

    public function routeToController(Request $request)
    {
        // Doing it lazy ;)
        return new Controllers\StaticResourceController($this);
    }
}
