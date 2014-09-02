<?php
namespace AnhNhan\Converge\Modules\Task\Transaction;

use AnhNhan\Converge\Modules\Task\Storage\TaskAssigned;
use AnhNhan\Converge\Modules\Task\Storage\TaskTag;
use AnhNhan\Converge\Modules\Task\Storage\TaskTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskEditor extends TransactionEditor
{
    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = TaskTransaction::TYPE_EDIT_LABEL;
        $types[] = TaskTransaction::TYPE_EDIT_DESC;
        $types[] = TaskTransaction::TYPE_EDIT_STATUS;
        $types[] = TaskTransaction::TYPE_EDIT_PRIORITY;
        $types[] = TaskTransaction::TYPE_EDIT_COMPLETED;
        $types[] = TaskTransaction::TYPE_ADD_COMMENT;
        $types[] = TaskTransaction::TYPE_ADD_ASSIGN;
        $types[] = TaskTransaction::TYPE_DEL_ASSIGN;
        $types[] = TaskTransaction::TYPE_ADD_TAG;
        $types[] = TaskTransaction::TYPE_DEL_TAG;
        $types[] = TaskTransaction::TYPE_ADD_RELATION;
        $types[] = TaskTransaction::TYPE_DEL_RELATION;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
            case TaskTransaction::TYPE_ADD_COMMENT:
            case TaskTransaction::TYPE_ADD_ASSIGN:
            case TaskTransaction::TYPE_ADD_TAG:
            case TaskTransaction::TYPE_ADD_RELATION:
                return null;
            case TaskTransaction::TYPE_DEL_ASSIGN:
            case TaskTransaction::TYPE_DEL_TAG:
            case TaskTransaction::TYPE_DEL_ASSIGN:
                return $transaction->newValue;
            case TaskTransaction::TYPE_EDIT_DESC:
                return $entity->description();
            case TaskTransaction::TYPE_EDIT_LABEL:
                return $entity->label();
            case TaskTransaction::TYPE_EDIT_STATUS:
                return $entity->status() ? $entity->status()->uid : null;
            case TaskTransaction::TYPE_EDIT_PRIORITY:
                return $entity->priority() ? $entity->priority()->uid : null;
            case TaskTransaction::TYPE_EDIT_COMPLETED:
                return $entity->completed();
        }
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
            case TaskTransaction::TYPE_ADD_COMMENT:
            case TaskTransaction::TYPE_ADD_ASSIGN:
            case TaskTransaction::TYPE_EDIT_DESC:
            case TaskTransaction::TYPE_EDIT_LABEL:
            case TaskTransaction::TYPE_EDIT_STATUS:
            case TaskTransaction::TYPE_EDIT_PRIORITY:
            case TaskTransaction::TYPE_EDIT_COMPLETED:
            case TaskTransaction::TYPE_ADD_TAG:
            case TaskTransaction::TYPE_ADD_RELATION:
                return $transaction->newValue();
            case TaskTransaction::TYPE_DEL_ASSIGN:
            case TaskTransaction::TYPE_DEL_TAG:
            case TaskTransaction::TYPE_DEL_RELATION:
                return null;
        }
    }

    protected function applyCustomTransactionEffects($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                $this->setPropertyPerReflection($entity, 'author', $transaction->actorId);
                $this->setPropertyPerReflection($entity, 'label', $transaction->newValue);
                $this->setPropertyPerReflection($entity, 'label_canonical', to_canonical($transaction->newValue));
                break;
            case TaskTransaction::TYPE_EDIT_LABEL:
                $this->setPropertyPerReflection($entity, 'label', $transaction->newValue);
                break;
            case TaskTransaction::TYPE_EDIT_DESC:
                $this->setPropertyPerReflection($entity, 'description', $transaction->newValue);
                break;
            case TaskTransaction::TYPE_EDIT_STATUS:
                $this->setPropertyPerReflection($entity, 'status', $transaction->newValue);
                $this->setPropertyPerReflection($transaction, 'newValue', $transaction->newValue->uid);
                break;
            case TaskTransaction::TYPE_EDIT_PRIORITY:
                $this->setPropertyPerReflection($entity, 'priority', $transaction->newValue);
                $this->setPropertyPerReflection($transaction, 'newValue', $transaction->newValue->uid);
                break;
            case TaskTransaction::TYPE_EDIT_COMPLETED:
                $this->setPropertyPerReflection($entity, 'completed', $transaction->newValue);
                break;
            case TaskTransaction::TYPE_ADD_COMMENT:
            case TaskTransaction::TYPE_ADD_ASSIGN:
            case TaskTransaction::TYPE_DEL_ASSIGN:
                // Do nothing
                break;
            case TaskTransaction::TYPE_ADD_TAG:
                $taskTag = new TaskTag($entity, $transaction->newValue);
                $this->persistLater($taskTag);
                break;
            case TaskTransaction::TYPE_DEL_TAG:
                $taskTag = new TaskTag($entity, $transaction->oldValue);
                $taskTag = $this->em()->merge($taskTag);
                $this->em()->remove($taskTag);
                break;
            case TaskTransaction::TYPE_ADD_RELATION:
                $this->persistLater($transaction->newValue);
                break;
            case TaskTransaction::TYPE_DEL_RELATION:
                $this->em()->remove($this->em()->merge($transaction->oldValue));
                break;
        }

        $entity->updateModifiedAt();
    }

    public function postApplyHook($entity, array $transactions)
    {
        $grpd_xacts = mgroup($transactions, "type");
        $assign_add_xacts = idx($grpd_xacts, TaskTransaction::TYPE_ADD_ASSIGN, []);

        foreach ($assign_add_xacts as $xact)
        {
            $taskAssign = new TaskAssigned($entity, $xact->newValue);
            $this->em()->persist($taskAssign);
        }

        $assign_del_xacts = idx($grpd_xacts, TaskTransaction::TYPE_DEL_ASSIGN, []);

        foreach ($assign_del_xacts as $xact)
        {
            $taskAssign = new TaskAssigned($entity, $xact->oldValue);
            $this->em()->remove($this->em()->merge($taskAssign));
        }
        $this->em()->persist($entity);

        $this->finalFlush();
    }
}
