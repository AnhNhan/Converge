<?php
namespace AnhNhan\ModHub\Modules\Front;

use AnhNhan\ModHub\Web\Application\BaseApplication;
use YamwLibs\Libs\Http\Request;
use YamwLibs\Libs\Routing\Route;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class FrontApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Front Site";
    }

    public function getInternalName()
    {
        return "front";
    }

    public function getRoutes()
    {
        return array(
            new Route("Std-route", "/", $this),
        );
    }

    public function routeToController(Request $request)
    {
        // Doing it lazy ;)
        return new Controllers\StandardFrontController($this);
    }
}
