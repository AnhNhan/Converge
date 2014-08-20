<?php
namespace AnhNhan\Converge\Modules\Activity;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class ActivityRecorder
{
    /**
     * @var array
     *      Return somethine like
     * [
     *      TransactionEntity::TYPE_CREATE => true,
     * ]
     */
    abstract public function getRecordedTransactionTypes();

    private $entity_manager;

    final public function __construct($app_or_em)
    {
        if (is_object($app_or_em) && $app_or_em instanceof ActivityApplication) {
            $this->entity_manager = $app_or_em->getEntityManager();
        } else if (is_object($app_or_em) && $app_or_em instanceof EntityManager) {
            $this->entity_manager = $app_or_em;
        } else {
            throw new \InvalidArgumentException(sprintf("Invalid type: '%s'", is_object($app_or_em) ? get_class($app_or_em) : gettype($app_or_em)));
        }
    }

    final public function record(array $xacts, $flush = true)
    {
        assert_instances_of($xacts, 'AnhNhan\Converge\Storage\Transaction\TransactionEntity');

        $acceptable_types = $this->getRecordedTransactionTypes();

        $activities = [];
        foreach ($xacts as $xact)
        {
            if (!isset($acceptable_types[$xact->type]))
            {
                continue;
            }

            $activity = new Storage\RecordedActivity;
            $activity->object_uid = $this->get_object_uid($xact);
            $activity->object_label = $this->get_object_label($xact);
            $activity->object_link = $this->get_object_link($xact);
            $activity->actor_uid = $this->get_actor_uid($xact);
            $activity->xact_uid = $this->get_xact_uid($xact);
            $activity->xact_type = $this->get_xact_type($xact);
            $activity->xact_contents = $this->get_xact_contents($xact);

            $activities[] = $activity;
        }

        array_map([$this->entity_manager, 'persist'], $activities);
        $flush and $this->entity_manager->flush();
    }

    final public function flush()
    {
        $this->entity_manager->flush();
    }

    protected function get_object_uid(TransactionEntity $xact)
    {
        return $xact->object->uid;
    }

    abstract protected function get_object_label(TransactionEntity $xact);

    protected function get_object_link(TransactionEntity $xact)
    {
        return null;
    }

    protected function get_actor_uid(TransactionEntity $xact)
    {
        return $xact->actorId;
    }

    protected function get_xact_uid(TransactionEntity $xact)
    {
        return $xact->uid;
    }

    protected function get_xact_type(TransactionEntity $xact)
    {
        return $xact->type;
    }

    protected function get_xact_contents(TransactionEntity $xact)
    {
        return $xact->newValue;
    }
}
