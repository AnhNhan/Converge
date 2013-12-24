<?php
namespace AnhNhan\ModHub\Modules\User;

use AnhNhan\ModHub\Web\Application\BaseApplication;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

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

    public function routeToController(\YamwLibs\Libs\Http\Request $request)
    {
        return null;
    }

    protected function buildEntityManager($dbConfig)
    {
        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/Storage"), $isDevMode);

        return EntityManager::create($dbConfig, $config);
    }
}
