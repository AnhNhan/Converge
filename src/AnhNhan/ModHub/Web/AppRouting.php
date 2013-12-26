<?php
namespace AnhNhan\ModHub\Web;

use YamwLibs\Libs\Routing\Router;

use Symfony\Component\HttpFoundation\Request;

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

    public function routeToApplication(Request $request)
    {
        if (!count($this->appRoutes)) {
            throw new \RuntimeException("We have no routes. Check your applications!");
        }

        $routingResult = $this->router->route($request->query->get("page", "/"));
        if ($routingResult) {
            $app = $routingResult["target"];
            unset($routingResult["target"]);

            $routeName = $routingResult["route"]->getName();

            $request->request->add($routingResult);
            $request->attributes->add(array("route-name" => $routeName));

            return $app;
        } else {
            // TODO: Get default/404 controller
            return null;
        }
    }
}
