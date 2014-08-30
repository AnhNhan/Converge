<?php
namespace AnhNhan\Converge\Modules\Newsroom;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class NewsroomApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Newsroom";
    }

    public function getInternalName()
    {
        return "newsroom";
    }

    // TODO: Only include them during article rendering - they can be annoying
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
        $routeName = $request->attributes->get("route-name");
        $controller = null;
        switch ($routeName) {
            case "article-display-test":
                $controller = new Controllers\DisplayTest($this);
                break;
            case "article-edit":
                // TODO: Dispatch by article type
                $controller = new Controllers\DMAEdit($this);
                break;
            case "article-display":
                // TODO: Dispatch by article type
                $controller = new Controllers\DMADisplay($this);
                break;
            case "channel-edit":
                $controller = new Controllers\ChannelEdit($this);
                break;
        }
        return $controller;
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . "/Storage"));
    }
}
