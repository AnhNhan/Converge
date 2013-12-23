<?php
namespace AnhNhan\ModHub\Modules\Forum\Events;

use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag;
use AnhNhan\ModHub\Modules\Tag\TagApplication;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionTagExternalEntityLoader
{
    /**
     * @var EntityManager
     */
    private $tagEntityManager;

    public function __construct()
    {
        $tagApp = new TagApplication;
        $this->tagEntityManager = $tagApp->getEntityManager();
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $dTag = $eventArgs->getEntity();
        if (!($dTag instanceof DiscussionTag)) {
            return;
        }

        $forumEntityManager = $eventArgs->getEntityManager();
        $discussionReflProp = $forumEntityManager->getClassMetadata('AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag')
            ->reflClass->getProperty('t_obj');
        $discussionReflProp->setAccessible(true);
        $discussionReflProp->setValue(
            $dTag, $this->tagEntityManager->getReference('AnhNhan\ModHub\Modules\Tag\Storage\Tag', $dTag->tagId())
        );
    }
}
