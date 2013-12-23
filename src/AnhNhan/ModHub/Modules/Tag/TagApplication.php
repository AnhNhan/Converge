<?php
namespace AnhNhan\ModHub\Modules\Tag;

use AnhNhan\ModHub\Web\Application\BaseApplication;
use YamwLibs\Libs\Http\Request;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

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
        return array();
    }

    public function routeToController(Request $request)
    {
        // TODO:
        return null;
    }

    public function getEntityManager()
    {
        static $em;
        if (!$em) {
            $em = $this->buildEntityManager();
        }

        return $em;
    }

    private function buildEntityManager()
    {
        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/Storage"), $isDevMode);

        return EntityManager::create($this->getDatabaseConfigForDoctrine(), $config);
    }
}
