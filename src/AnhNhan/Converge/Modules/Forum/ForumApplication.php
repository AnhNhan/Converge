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

    public function getRegisteredEventListeners()
    {
        $discussionRecorder = new Activity\DiscussionRecorder($this->getExternalApplication('activity'));
        $postRecorder = new Activity\PostRecorder($this->getExternalApplication('activity'));

        return [
            [
                'event.name' => \Event_DiscussionTransaction_Record,
                'event.listener' => function (ArrayDataEvent $event, $event_name, $dispatcher) use ($recorder)
                {
                    if (!$event->check_array_object_type('AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction'))
                    {
                        throw new \InvalidArgumentException('Received invalid object array.');
                    }

                    $discussionRecorder->record($event->data);
                },
            ],
            [
                'event.name' => \Event_PostTransaction_Record,
                'event.listener' => function (ArrayDataEvent $event, $event_name, $dispatcher) use ($recorder)
                {
                    if (!$event->check_array_object_type('AnhNhan\Converge\Modules\Forum\Storage\PostTransaction'))
                    {
                        throw new \InvalidArgumentException('Received invalid object array.');
                    }

                    $postRecorder->record($event->data);
                },
            ],
        ];
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
