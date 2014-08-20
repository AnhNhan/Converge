<?php
namespace AnhNhan\Converge\Storage\Transaction;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Web\Application\BaseApplication;

use Doctrine\ORM\EntityManager;

/**
 * Inspired by Pharicator's ApplicationTransaction && ApplicationTransactionEditor
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class TransactionEditor
{
    // TODO: Decide whether this should be a UID or an object
    private $actor;

    /**
     * @var EntityManager
     */
    private $entityManager;

    const NO_EFFECT_ERROR  = "noeffect.error";
    const NO_EFFECT_SKIP   = "noeffect.skip";
    const NO_EFFECT_IGNORE = "noeffect.ignore";

    private $noEffectBehaviour = self::NO_EFFECT_SKIP;

    const FLUSH_DONT_FLUSH = "flush.dontflush";
    const FLUSH_FLUSH      = "flush.flush";
    const FLUSH_DEFER      = "flush.defer_flush";

    private $flushBehaviour = self::FLUSH_DEFER;

    final public function __construct($appOrEm)
    {
        if ($appOrEm instanceof BaseApplication) {
            $this->entityManager = $appOrEm->getEntityManager();
        } else if ($appOrEm instanceof EntityManager) {
            $this->entityManager = $appOrEm;
        } else {
            throw new \InvalidArgumentException(sprintf("Invalid type: '%s'", get_class($appOrEm)));
        }
    }

    /**
     * Convenience method for fast chainability
     */
    final public static function create($appOrEm)
    {
        return new static($appOrEm);
    }

    final public function setActor($actor)
    {
        $this->actor = $actor;
        return $this;
    }

    final public function actor()
    {
        return $this->actor;
    }

    final public function em()
    {
        return $this->entityManager;
    }

    private function persistTransaction(TransactionEntity $transaction)
    {
        return $this->em()->persist($transaction);
    }

    private $persistLater = array();

    final protected function persistLater(EntityDefinition $entity)
    {
        $this->persistLater[] = $entity;
        return $this;
    }

    private $entity;

    final public function setEntity(EntityDefinition $entity)
    {
        if ($entity instanceof TransactionEntity) {
            throw new \InvalidException("Can't apply transactions on " . get_class($entity));
        }

        $this->entity = $entity;
        return $this;
    }

    final public function setBehaviourOnNoEffect($behaviour = self::NO_EFFECT_SKIP)
    {
        $this->noEffectBehaviour = $behaviour;
        return $this;
    }

    final public function behaviourOnNoEffect()
    {
        return $this->noEffectBehaviour;
    }

    final public function setFlushBehaviour($behaviour = self::FLUSH_FLUSH)
    {
        $this->flushBehaviour = $behaviour;
        return $this;
    }

    final public function flushBehaviour()
    {
        return $this->flushBehaviour;
    }

    private $transactions = array();

    final public function addTransaction(TransactionEntity $transaction)
    {
        $this->transactions[] = $transaction;
        return $this;
    }

    final public function clearTransactions()
    {
        $this->transactions = array();
        return $this;
    }

    final public function apply()
    {
        if (!$this->entity) {
            throw new \Exception("You have to assign an entity through setEntity().");
        }

        if (!$this->actor()) {
            throw new \Exception("We require an actor. Please set one through setActor().");
        }

        $object   = $this->entity;
        $xactions = $this->transactions;
        $isNewObject = $object->uid() === null;

        foreach ($xactions as $xact) {
            if (null !== coalesce(
                $xact->uid(),
                $xact->actorId(),
                $xact->object(),
                $xact->oldValue()
            )) {
                throw new \InvalidArgumentException("Transaction already had a value set. Can't be applied!");
            }

            $xact->setActorId($this->actor());
            $xact->setObject($object);
        }

        $firstXAct = true;
        foreach ($xactions as $xact) {
            if ($isNewObject && $firstXAct) {
                $oldValue = null;
            } else {
                $oldValue = $this->getTransactionOldValue($object, $xact);
            }

            $xact->setOldValue($oldValue);

            $newValue = $this->getTransactionNewValue($object, $xact);
            $xact->setNewValue($newValue);

            $firstXAct = false;
        }

        foreach ($xactions as $key => $xact) {
            $noEffect = !$this->transactionHasEffect($object, $xact);

            if ($noEffect) {
                switch ($this->noEffectBehaviour) {
                    case self::NO_EFFECT_SKIP:
                        unset($xactions[$key]);
                        continue;
                        break;
                    case self::NO_EFFECT_IGNORE:
                        // <no action>
                        break;
                    case self::NO_EFFECT_ERROR:
                        // TODO: Better error message & custom exception
                        throw new \Exception("Transaction has no effect!");
                        break;
                }
            }

            $this->applyTransactionEffects($object, $xact);
        }

        foreach ($xactions as $xact) {
            $this->persistTransaction($xact);
        }

        $this->em()->persist($object);
        $this->flushBehaviour == self::FLUSH_FLUSH && $this->em()->flush();

        foreach ($this->persistLater as $entity) {
            $this->em()->persist($entity);
        }

        $this->finalFlush();

        $this->postApplyHook($object, $xactions);

        return $xactions;
    }

    protected function finalFlush()
    {
        $this->flushBehaviour != self::FLUSH_DONT_FLUSH && $this->em()->flush();
    }

    protected function postApplyHook($entity, array $xactions)
    {
        // <empty>
    }

    public function getTransactionTypes()
    {
        $types = array();

        $types[] = TransactionEntity::TYPE_CREATE;

        return $types;
    }

    private function getTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            default:
                return $this->getCustomTransactionOldValue($entity, $transaction);
                break;
        }
    }

    abstract protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction);

    private function getTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            default:
                return $this->getCustomTransactionNewValue($entity, $transaction);
                break;
        }
    }

    abstract protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction);

    private function applyTransactionEffects($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            default:
                return $this->applyCustomTransactionEffects($entity, $transaction);
                break;
        }
    }

    abstract protected function applyCustomTransactionEffects($entity, TransactionEntity $transaction);

    protected function transactionHasEffect($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                return true;
            default:
                return $transaction->oldValue() !== $transaction->newValue();
                break;
        }
    }

    final protected function setPropertyPerReflection($object, $name, $value)
    {
        $userReflProp = $this->em()->getClassMetadata(get_class($object))
            ->reflClass->getProperty($name);
        $userReflProp->setAccessible(true);
        $userReflProp->setValue(
            $object, $value
        );
    }
}
