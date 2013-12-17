<?php
namespace AnhNhan\ModHub\Modules\Examples;

use AnhNhan\ModHub\Web\Application\BaseApplication;
use YamwLibs\Libs\Http\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ExamplesApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Examples";
    }

    public function getInternalName()
    {
        return "examples";
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . "/resources/routes.yml");
    }

    public function routeToController(Request $request)
    {
        // Doing it lazy ;)
        return new Controllers\StandardExamplesController($this);
    }
}
