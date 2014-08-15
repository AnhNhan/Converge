<?php
namespace AnhNhan\Converge\Modules\Task\Transaction;

use AnhNhan\Converge\Modules\Task\Storage\TaskPriorityTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskPriorityEditor extends TransactionEditor
{
    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = TaskPriorityTransaction::TYPE_EDIT_LABEL;
        $types[] = TaskPriorityTransaction::TYPE_EDIT_ORDER;
        $types[] = TaskPriorityTransaction::TYPE_EDIT_COLOR;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                return null;
            case TaskPriorityTransaction::TYPE_EDIT_COLOR:
                return $entity->color();
            case TaskPriorityTransaction::TYPE_EDIT_ORDER:
                return $entity->displayOrder();
            case TaskPriorityTransaction::TYPE_EDIT_LABEL:
                return $entity->label();
        }
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
            case TaskPriorityTransaction::TYPE_EDIT_COLOR:
            case TaskPriorityTransaction::TYPE_EDIT_ORDER:
            case TaskPriorityTransaction::TYPE_EDIT_LABEL:
                return $transaction->newValue();
        }
    }

    protected function applyCustomTransactionEffects($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                $this->setPropertyPerReflection($entity, 'label', $transaction->newValue);
                $this->setPropertyPerReflection($entity, 'label_canonical', to_canonical($transaction->newValue));
                break;
            case TaskPriorityTransaction::TYPE_EDIT_COLOR:
                $this->setPropertyPerReflection($entity, 'color', $transaction->newValue);
                break;
            case TaskPriorityTransaction::TYPE_EDIT_ORDER:
                $this->setPropertyPerReflection($entity, 'displayOrder', $transaction->newValue);
                break;
            case TaskPriorityTransaction::TYPE_EDIT_LABEL:
                $this->setPropertyPerReflection($entity, 'label', $transaction->newValue);
                break;
        }

        $entity->updateModifiedAt();
    }
}
