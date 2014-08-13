<?php
namespace AnhNhan\Converge\Modules\Search;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class SearchApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Search";
    }

    public function getInternalName()
    {
        return "search";
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . "/resources/routes.yml");
    }

    public function routeToController(Request $request)
    {
        switch ($request->attributes->get("route-name")) {
            case "autocomplete-tags":
                return new Controllers\AutocompleteTags($this);
                break;
            case "search-disq":
                return new Controllers\SearchDiscussion($this);
                break;
        }

        return null;
    }
}
