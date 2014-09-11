<?php
namespace AnhNhan\Converge\Modules\Forum;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ForumApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return 'Forum';
    }

    public function getInternalName()
    {
        return 'forum';
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . '/resources/routes.yml');
    }

    public function getActivityRenderers()
    {
        return [
            'DISQ' => $this->createActivityRenderer('disq_activity_label', 'disq_activity_body', 'forum_activity_class'),
            'POST' => $this->createActivityRenderer('post_activity_label', 'post_activity_body', 'forum_activity_class'),
        ];
    }

    public function routeToController(Request $request)
    {
        $routeName = $request->attributes->get('route-name');

        switch ($routeName) {
            case 'main-listing':
                return new Controllers\DiscussionListingController($this);
                break;
            case 'disq-creation':
                return new Controllers\DiscussionEditController($this);
                break;
            case 'disq-display':
                return new Controllers\DiscussionDisplayController($this);
                break;
            case 'disq-posting':
                return new Controllers\PostEditController($this);
                break;
            case 'forum-comment':
                return new Controllers\Comment($this);
                break;
        }

        return null;
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . '/Storage'));
    }
}
