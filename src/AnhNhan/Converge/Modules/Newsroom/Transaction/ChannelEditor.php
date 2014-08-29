<?php
namespace AnhNhan\Converge\Modules\Newsroom\Transaction;

use AnhNhan\Converge\Modules\Newsroom\Storage\ChannelTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ChannelEditor extends TransactionEditor
{
    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = ChannelTransaction::TYPE_EDIT_LABEL;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                return null;
            case ChannelTransaction::TYPE_EDIT_LABEL:
                return $entity->label();
        }
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
            case ChannelTransaction::TYPE_EDIT_LABEL:
                return $transaction->newValue();
        }
    }

    protected function applyCustomTransactionEffects($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                $this->setPropertyPerReflection($entity, 'slug', to_slug($transaction->newValue));
                break;
            case ChannelTransaction::TYPE_EDIT_LABEL:
                $this->setPropertyPerReflection($entity, 'label', $transaction->newValue);
                break;
        }

        $entity->updateModifiedAt();
    }
}
