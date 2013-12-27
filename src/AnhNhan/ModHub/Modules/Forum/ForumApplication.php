<?php
namespace AnhNhan\ModHub\Modules\Forum;

use AnhNhan\ModHub\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

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
        $routeName = $request->attributes->get("route-name");

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

    protected function buildEntityManager($dbConfig)
    {
        $entityManager = $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . "/Storage"));
        $eventManager  = $entityManager->getEventManager();

        $eventManager->addEventListener(
            array(\Doctrine\ORM\Events::postLoad),
            new Events\DiscussionTagExternalEntityLoader($this->getExternalApplication("tag"))
        );

        return $entityManager;
    }
}
