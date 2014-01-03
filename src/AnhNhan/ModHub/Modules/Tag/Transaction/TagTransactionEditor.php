<?php
namespace AnhNhan\ModHub\Modules\Tag\Transaction;

use AnhNhan\ModHub\Modules\Tag\Storage\TagTransaction;
use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;
use AnhNhan\ModHub\Storage\Transaction\TransactionEditor;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TagTransactionEditor extends TransactionEditor
{
    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = TagTransaction::TYPE_EDIT_LABEL;
        $types[] = TagTransaction::TYPE_EDIT_DESC;
        $types[] = TagTransaction::TYPE_EDIT_COLOR;
        $types[] = TagTransaction::TYPE_EDIT_ORDER;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                return null;
            case TagTransaction::TYPE_EDIT_LABEL:
                return $entity->label();
            case TagTransaction::TYPE_EDIT_DESC:
                return $entity->description();
            case TagTransaction::TYPE_EDIT_COLOR:
                return $entity->color();
            case TagTransaction::TYPE_EDIT_ORDER:
                return $entity->displayOrder();
        }
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                return null;
            case TagTransaction::TYPE_EDIT_LABEL:
            case TagTransaction::TYPE_EDIT_DESC:
            case TagTransaction::TYPE_EDIT_COLOR:
                return $transaction->newValue() ?: null;
            case TagTransaction::TYPE_EDIT_ORDER:
                return $transaction->newValue() ?: 0;
        }
    }

    protected function applyCustomTransactionEffects($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TagTransaction::TYPE_EDIT_LABEL:
                $entity->setLabel($transaction->newValue());
                break;
            case TagTransaction::TYPE_EDIT_DESC:
                $entity->setDescription($transaction->newValue());
                break;
            case TagTransaction::TYPE_EDIT_COLOR:
                $entity->setColor($transaction->newValue());
                break;
            case TagTransaction::TYPE_EDIT_ORDER:
                $entity->setDisplayOrder($transaction->newValue());
                break;
        }

        $entity->updateModifiedDate();
    }
}
