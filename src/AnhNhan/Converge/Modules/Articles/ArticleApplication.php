<?php
namespace AnhNhan\Converge\Modules\Articles;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ArticleApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Article";
    }

    public function getInternalName()
    {
        return "article";
    }

    public function getCustomMarkupRules()
    {
        return [
            new Markup\CenterText,
            new Markup\FancyHeader,
            new Markup\FontSize,
        ];
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
        }
        return $controller;
    }
}
