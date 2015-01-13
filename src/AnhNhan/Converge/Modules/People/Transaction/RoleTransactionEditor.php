<?php
namespace AnhNhan\Converge\Modules\People\Transaction;

use AnhNhan\Converge\Modules\People\Storage\RoleTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class RoleTransactionEditor extends TransactionEditor
{
    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = RoleTransaction::TYPE_EDIT_LABEL;
        $types[] = RoleTransaction::TYPE_EDIT_DESC;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity,
        TransactionEntity $transaction)
    {
        switch ($transaction->type) {
            case TransactionEntity::TYPE_CREATE:
                return null;
            case RoleTransaction::TYPE_EDIT_LABEL:
                return $entity->label;
            case RoleTransaction::TYPE_EDIT_DESC:
                return $entity->description;
        }
    }

    protected function getCustomTransactionNewValue($entity,
        TransactionEntity $transaction)
    {
        switch ($transaction->type) {
            case RoleTransaction::TYPE_EDIT_LABEL:
            case RoleTransaction::TYPE_EDIT_DESC:
                return $transaction->newValue;
            case TransactionEntity::TYPE_CREATE: // Set to name
                if (!$transaction->newValue) {
                    throw new \Exception(
                        "You have to set \$transaction->newValue to the role name!"
                    );
                }
                return $transaction->newValue;
        }
    }

    protected function applyCustomTransactionEffects($entity,
        TransactionEntity $transaction)
    {
        switch ($transaction->type) {
            case TransactionEntity::TYPE_CREATE:
                $this->setPropertyPerReflection($entity, 'name', $transaction->newValue);
                break;
            case RoleTransaction::TYPE_EDIT_LABEL:
                $entity->setLabel($transaction->newValue);
                break;
            case RoleTransaction::TYPE_EDIT_DESC:
                $entity->setDescription($transaction->newValue);
                break;
        }

        $entity->updateModifiedAt();
    }
}
