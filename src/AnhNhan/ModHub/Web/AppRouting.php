<?php
namespace AnhNhan\ModHub\Web;

use YamwLibs\Libs\Routing\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class AppRouting implements RequestMatcherInterface, UrlGeneratorInterface
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

    public function matchRequest(Request $request)
    {
        $parameters = $this->router->route($request->getPathInfo());

        if (false === $parameters) {
            throw new \Exception(sprintf("Could not find a route for '%s'", $request->getPathInfo()));
        }

        // Compat with Symfony conventions
        $parameters["_route"] = idx($parameters, "route-name");
        return $parameters;
    }

    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        if ($referenceType === self::ABSOLUTE_URL) {
            $usesId = false;
            array_map(function ($entry) use ($usesId) {
                if (strpos($entry, "id") !== false) {
                    $usesId = true;
                }
            }, $parameters);

            $routes = mgroup($this->appRoutes, "getName");
            $route  = idx($routes, $name, array());
            if ($route && count($route) == 1) {
                return Request::createFromGlobals()->getSchemeAndHttpHost() . $route->getPattern();
            } else {
                throw new \Exception("Routes with parameters can't be generated yet!");
            }
        } else {
            throw new \Exception("Reference type not supported.");
        }
    }

    public function routeToApplication(Request $request)
    {
        if (!count($this->appRoutes)) {
            throw new \RuntimeException("We have no routes. Check your applications!");
        }

        $routingResult = $this->matchRequest($request);
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

    // Stubs for stupid interfaces
    public function setContext(\Symfony\Component\Routing\RequestContext $context)
    {
    }
    public function getContext()
    {
    }
}
