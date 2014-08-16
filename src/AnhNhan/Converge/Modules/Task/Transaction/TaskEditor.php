<?php
namespace AnhNhan\Converge\Modules\Task\Transaction;

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
        $types[] = TaskTransaction::TYPE_EDIT_ASSIGN;
        $types[] = TaskTransaction::TYPE_EDIT_DESC;
        $types[] = TaskTransaction::TYPE_EDIT_STATUS;
        $types[] = TaskTransaction::TYPE_EDIT_PRIORITY;
        $types[] = TaskTransaction::TYPE_EDIT_COMPLETED;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                return null;
            case TaskTransaction::TYPE_EDIT_DESC:
                return $entity->description();
            case TaskTransaction::TYPE_EDIT_ASSIGN:
                return $entity->assignedId();
            case TaskTransaction::TYPE_EDIT_LABEL:
                return $entity->label();
            case TaskTransaction::TYPE_EDIT_STATUS:
                return $entity->status();
            case TaskTransaction::TYPE_EDIT_PRIORITY:
                return $entity->priority();
            case TaskTransaction::TYPE_EDIT_COMPLETED:
                return $entity->completed();
        }
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
            case TaskTransaction::TYPE_EDIT_DESC:
            case TaskTransaction::TYPE_EDIT_LABEL:
            case TaskTransaction::TYPE_EDIT_STATUS:
            case TaskTransaction::TYPE_EDIT_PRIORITY:
            case TaskTransaction::TYPE_EDIT_COMPLETED:
            case TaskTransaction::TYPE_EDIT_ASSIGN:
                return $transaction->newValue();
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
            case TaskTransaction::TYPE_EDIT_ASSIGN:
                $this->setPropertyPerReflection($entity, 'assigned', $transaction->newValue);
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
        }

        $entity->updateModifiedAt();
    }
}
