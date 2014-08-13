<?php
namespace AnhNhan\Converge\Storage\Doctrine;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Types\UID;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class LifeCycleUIDGenerator implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $e)
    {
        $em = $e->getEntityManager();
        $unitOfWork = $em->getUnitOfWork();
        foreach (array_filter($unitOfWork->getScheduledEntityInsertions(), function ($ent) { return method_exists($ent, 'uid'); }) as $entity) {
            $classMetadata = $em->getClassMetadata(get_class($entity));
            $userReflProp = $classMetadata
                ->reflClass->getProperty('uid');
            $userReflProp->setAccessible(true);
            $userReflProp->setValue(
                $entity, UID::generate($entity->getUIDType())
            );
            $unitOfWork->recomputeSingleEntityChangeSet($classMetadata, $entity);
        }
    }
}
