<?php
namespace AnhNhan\ModHub\Modules\Markup;

use AnhNhan\ModHub\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class MarkupApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Markup";
    }

    public function getInternalName()
    {
        return "markup";
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . "/resources/routes.yml");
    }

    public function routeToController(Request $request)
    {
        $url = trim($request->getPathInfo(), "/ ");
        switch ($url) {
            case "markup/test":
                $controller = new Controllers\MarkupTestingController($this);
                break;
            case "markup/process":
                $controller = new Controllers\MarkupProcessingController($this);
                break;
            case "markup/help":
                throw new \Exception("Page does not exist!");
                break;
            default:
                throw new \Exception("Page does not exist!");
                break;
        }
        return $controller;
    }
}
