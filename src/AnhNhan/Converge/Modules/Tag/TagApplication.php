<?php
namespace AnhNhan\Converge\Modules\Tag;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TagApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Discussion Tags";
    }

    public function getInternalName()
    {
        return "tag";
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . "/resources/routes.yml");
    }

    public function routeToController(Request $request)
    {
        $routeName = $request->attributes->get("route-name");

        switch ($routeName) {
            case "tag-listing":
                return new Controllers\TagListingController($this);
                break;
            case "tag-creation":
                return new Controllers\TagCreationController($this);
                break;
            case "tag-display":
                return new Controllers\TagDisplayController($this);
                break;
        }

        return null;
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . "/Storage"));
    }
}
