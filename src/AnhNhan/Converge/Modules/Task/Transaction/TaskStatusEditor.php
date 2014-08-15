<?php
namespace AnhNhan\Converge\Modules\Task\Transaction;

use AnhNhan\Converge\Modules\Task\Storage\TaskStatusTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskStatusEditor extends TransactionEditor
{
    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = TaskStatusTransaction::TYPE_EDIT_LABEL;
        $types[] = TaskStatusTransaction::TYPE_EDIT_COLOR;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                return null;
            case TaskStatusTransaction::TYPE_EDIT_COLOR:
                return $entity->color();
            case TaskStatusTransaction::TYPE_EDIT_LABEL:
                return $entity->label();
        }
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
            case TaskStatusTransaction::TYPE_EDIT_COLOR:
            case TaskStatusTransaction::TYPE_EDIT_LABEL:
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
            case TaskStatusTransaction::TYPE_EDIT_COLOR:
                $this->setPropertyPerReflection($entity, 'color', $transaction->newValue);
                break;
            case TaskStatusTransaction::TYPE_EDIT_LABEL:
                $this->setPropertyPerReflection($entity, 'label', $transaction->newValue);
                break;
        }

        $entity->updateModifiedAt();
    }
}
