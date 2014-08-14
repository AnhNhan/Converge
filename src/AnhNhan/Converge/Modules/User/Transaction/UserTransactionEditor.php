<?php
namespace AnhNhan\Converge\Modules\User\Transaction;

use AnhNhan\Converge\Modules\User\Storage\User;
use AnhNhan\Converge\Modules\User\Storage\UserTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class UserTransactionEditor extends TransactionEditor
{
    const SALT_PW_SEPARATOR = "||@@$$@@||";

    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = UserTransaction::TYPE_EDIT_PASSWORD;
        $types[] = UserTransaction::TYPE_ADD_ROLE;
        $types[] = UserTransaction::TYPE_REMOVE_ROLE;
        $types[] = UserTransaction::TYPE_ADD_EMAIL;
        $types[] = UserTransaction::TYPE_REMOVE_EMAIL;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type) {
            case TransactionEntity::TYPE_CREATE:
            case UserTransaction::TYPE_ADD_ROLE:
            case UserTransaction::TYPE_ADD_EMAIL:
                return null;
            case UserTransaction::TYPE_EDIT_PASSWORD: // Fuzzy pw
                return "<hazy>";
            case UserTransaction::TYPE_REMOVE_ROLE: // Set old value to role UID
            case UserTransaction::TYPE_REMOVE_EMAIL:
                return $transaction->newValue();
        }
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        if (TransactionEntity::TYPE_CREATE == $transaction->type) {
            if (!$transaction->newValue) {
                throw new \Exception("You need to supply the username in the newValue");
            }
        }
        if (UserTransaction::TYPE_EDIT_PASSWORD == $transaction->type) {
            if (false === strpos($transaction->newValue, self::SALT_PW_SEPARATOR)) {
                throw new \Exception("You need to supply both salt and password in the newValue");
            }
        }
        switch ($transaction->type) {
            case TransactionEntity::TYPE_CREATE:
            case UserTransaction::TYPE_EDIT_PASSWORD:
            case UserTransaction::TYPE_ADD_ROLE:
            case UserTransaction::TYPE_ADD_EMAIL:
                return $transaction->newValue;
            case UserTransaction::TYPE_REMOVE_ROLE: // Set old value to role UID
            case UserTransaction::TYPE_REMOVE_EMAIL:
                return null;
        }
    }

    protected function applyCustomTransactionEffects($entity, TransactionEntity $transaction)
    {
        static $entityRole = 'AnhNhan\Converge\Modules\User\Storage\Role';
        static $entityEmail = 'AnhNhan\Converge\Modules\User\Storage\Email';
        // TODO: Put this in some pre-apply validation hook - we're probably terminating a little bit too late here
        if (in_array($transaction->type, array(UserTransaction::TYPE_ADD_ROLE, UserTransaction::TYPE_REMOVE_ROLE))) {
            $roleId = $transaction->type == UserTransaction::TYPE_ADD_ROLE ? $transaction->newValue : $transaction->oldValue;
            $role = id(new \AnhNhan\Converge\Modules\User\Query\RoleQuery($this->em()))->retrieveRole($roleId);
            if (!$role) {
                throw new \Exception("Role {$roleId} does not exist!");
            }
        }
        switch ($transaction->type) {
            case TransactionEntity::TYPE_CREATE:
                $this->setPropertyPerReflection($entity, 'username', $transaction->newValue);
                $this->setPropertyPerReflection($entity, 'name_canon', User::to_canonical($transaction->newValue));
                break;
            case UserTransaction::TYPE_EDIT_PASSWORD:
                list($salt, $password) = explode(self::SALT_PW_SEPARATOR, $transaction->newValue);
                $entity->updatePassword($password, $salt);
                $transaction->setNewValue("<fuzzy>");
                break;
            case UserTransaction::TYPE_ADD_ROLE:
                $entity->addRole($role);
                break;
            case UserTransaction::TYPE_REMOVE_ROLE:
                $entity->removeRole($role);
                break;
            case UserTransaction::TYPE_ADD_EMAIL:
                break;
            case UserTransaction::TYPE_REMOVE_EMAIL:
                break;
        }

        $entity->updateModifiedAt();
    }
}
