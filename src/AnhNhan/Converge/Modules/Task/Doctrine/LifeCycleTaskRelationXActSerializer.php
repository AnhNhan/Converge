<?php
namespace AnhNhan\Converge\Modules\Task\Doctrine;

use AnhNhan\Converge\Modules\Task\Storage\TaskRelation;
use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Types\UID;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class LifeCycleTaskRelationXActSerializer implements EventSubscriber
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
        $filtered_xacts = array_filter(
            $unitOfWork->getScheduledEntityInsertions(),
            function ($xact)
            {
                $val =
                    is_object($xact)
                    && $xact instanceof TransactionEntity
                    && (
                        (is_object($xact->oldValue) && $xact->oldValue instanceof TaskRelation && method_exists($xact->oldValue, 'serializeForXAct'))
                        || (is_object($xact->newValue) && $xact->newValue instanceof TaskRelation && method_exists($xact->newValue, 'serializeForXAct'))
                        )
                ;
                return $val;
            }
        );
        foreach ($filtered_xacts as $xact) {
            $this->updateValue($em, $xact, 'oldValue');
            $this->updateValue($em, $xact, 'newValue');
            $classMetadata = $em->getClassMetadata(get_class($xact));
            $unitOfWork->recomputeSingleEntityChangeSet($classMetadata, $xact);
        }
    }

    private function updateValue($em, $xact, $property)
    {
        if (!$xact->$property)
        {
            return;
        }
        $classMetadata = $em->getClassMetadata(get_class($xact));
        $userReflProp = $classMetadata
            ->reflClass->getProperty($property);
        $userReflProp->setAccessible(true);
        $userReflProp->setValue(
            $xact, $xact->$property->serializeForXAct()
        );
    }
}
