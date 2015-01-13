<?php
namespace AnhNhan\Converge\Modules\Subscription;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class SubscriptionApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Subscription";
    }

    public function getInternalName()
    {
        return "subscription";
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . "/resources/routes.yml");
    }

    public function routeToController(Request $request)
    {
        $routeName = $request->attributes->get("route-name");

        switch ($routeName) {
            case "subscription-user-object":
                return new Controllers\Subscription($this);
                break;
        }

        return null;
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . "/Storage"));
    }
}
