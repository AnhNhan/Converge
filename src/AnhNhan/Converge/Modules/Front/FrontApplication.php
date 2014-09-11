<?php
namespace AnhNhan\Converge\Modules\Front;

use AnhNhan\Converge\Web\Application\BaseApplication;
use YamwLibs\Libs\Routing\Route;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class FrontApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return 'Front Site';
    }

    public function getInternalName()
    {
        return 'front';
    }

    public function getRoutes()
    {
        return array(
            new Route('std-route', '/', $this),
            new Route('dash-route', '/dash', $this),
        );
    }

    public function routeToController(Request $request)
    {
        switch ($request->attributes->get('route-name')) {
            case 'std-route':
                return new Controllers\StandardFrontController($this);
                break;
            case 'dash-route':
                return new Controllers\Dashboard($this);
                break;
        }

        return null;
    }
}
