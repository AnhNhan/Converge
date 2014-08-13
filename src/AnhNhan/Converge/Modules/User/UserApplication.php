<?php
namespace AnhNhan\Converge\Modules\User;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class UserApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "User Application";
    }

    public function getInternalName()
    {
        return "user";
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . "/resources/routes.yml");
    }

    public function routeToController(Request $request)
    {
        switch ($request->attributes->get("route-name")) {
            case "role-edit":
                return new Controllers\RoleEditController($this);
            case "role-listing":
                return new Controllers\RoleListingController($this);
            case "join":
                return new Controllers\UserRegisterController($this);
            case "login":
                return new Controllers\UserLoginForm($this);
            case "login_check":
                return new Controllers\UserLoginCheck($this);
            case "logout":
                return new Controllers\UserLogout($this);
            case "user-display":
                return new Controllers\UserDisplay($this);
        }

        return null;
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . "/Storage"));
    }
}
