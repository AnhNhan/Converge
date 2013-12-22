<?php
namespace AnhNhan\ModHub\Web;

use YamwLibs\Libs\Http\Request;
use YamwLibs\Libs\Routing\Router;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class AppRouting
{
    private $appList = array();
    private $appInstanceList = array();
    private $appRoutes = array();
    private $router;

    public function __construct(array $appList = array())
    {
        if ($appList) {
            $this->setAppList($appList);
        }
    }

    public function setAppList(array $appList)
    {
        $this->appList = $appList;
        $this->generateRouter();
        return $this;
    }

    private function generateRouter()
    {
        foreach ($this->appList as $class_name) {
            $this->appInstanceList[] = new $class_name;
        }

        assert_instances_of($this->appInstanceList, 'AnhNhan\ModHub\Web\Application\BaseApplication');

        $this->appRoutes = mpull($this->appInstanceList, "getRoutes");

        $router = new Router;
        foreach ($this->appRoutes as $routes) {
            foreach ($routes as $route) {
                $router->registerRoute($route);
            }
        }

        $this->router = $router;
    }

    /**
     * @return array The result dictionary from the router. The app is in $result["target"].
     */
    public function routeToApplication($uri)
    {
        if (!count($this->appRoutes)) {
            throw new \RuntimeException("We have no routes. Check your applications!");
        }

        return $this->router->route($uri);
    }

    public function routeToController(Request $request)
    {
        $routingResult = $this->routeToApplication($request->getValue("uri-action-string"));
        if ($routingResult) {
            $app = $routingResult["target"];

            $routeName = $routingResult["route"]->getName();
            $routingResult["route-name"] = $routeName;

            // Remove the target app again. It ain't supposed to appear in the Request
            unset($routingResult["target"]);
            unset($routingResult["route"]);
            $request->populate($routingResult);
            $controller = $app->routeToController($request);
            return $controller;
        } else {
            // TODO: Get default/404 controller
            return null;
        }
    }
}
