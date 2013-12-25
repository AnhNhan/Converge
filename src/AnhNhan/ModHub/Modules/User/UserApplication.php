<?php
namespace AnhNhan\ModHub\Modules\User;

use AnhNhan\ModHub\Web\Application\BaseApplication;

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
        return array();
    }

    public function routeToController(Request $request)
    {
        return null;
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . "/Storage"));
    }
}
