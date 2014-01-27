<?php
namespace AnhNhan\ModHub\Modules\Examples;

use AnhNhan\ModHub\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

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
        $routeName = $request->attributes->get("route-name");

        switch ($routeName) {
            case "example-display":
                return new Controllers\StandardExamplesController($this);
            case "example-listing":
                return new Controllers\ExampleListing($this);
        }
    }
}
