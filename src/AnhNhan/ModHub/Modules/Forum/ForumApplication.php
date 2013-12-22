<?php
namespace AnhNhan\ModHub\Modules\Forum;

use AnhNhan\ModHub\Web\Application\BaseApplication;
use YamwLibs\Libs\Http\Request;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ForumApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return "Forum";
    }

    public function getInternalName()
    {
        return "forum";
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . "/resources/routes.yml");
    }

    public function routeToController(Request $request)
    {
        $routeName = $request->getValue("route-name");

        switch ($routeName) {
            case "main-listing":
                return new Controllers\DiscussionListingController($this);
                break;
            case "disq-creation":
                return new Controllers\DiscussionCreationController($this);
                break;
            case "disq-display":
                return new Controllers\DiscussionDisplayController($this);
                break;
            case "disq-posting":
                return new Controllers\DiscussionPostingController($this);
                break;
        }

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

    public function buildEntityManager()
    {
        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/Storage"), $isDevMode);

        return EntityManager::create($this->getDatabaseConfigForDoctrine(), $config);
    }
}
