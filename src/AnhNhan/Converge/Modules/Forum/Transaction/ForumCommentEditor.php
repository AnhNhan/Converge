<?php
namespace AnhNhan\Converge\Modules\Forum\Transaction;

use AnhNhan\Converge\Modules\Forum\Storage\ForumCommentTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ForumCommentEditor extends TransactionEditor
{
    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = ForumCommentTransaction::TYPE_EDIT_TEXT;
        $types[] = ForumCommentTransaction::TYPE_EDIT_DELETED;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                return null;
            case ForumCommentTransaction::TYPE_EDIT_TEXT:
                return $entity->rawText;
            case ForumCommentTransaction::TYPE_EDIT_DELETED:
                return $entity->deleted;
        }
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
            case ForumCommentTransaction::TYPE_EDIT_TEXT:
            case ForumCommentTransaction::TYPE_EDIT_DELETED:
                return $transaction->newValue;
        }
    }

    protected function applyCustomTransactionEffects($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                $this->setPropertyPerReflection($entity, 'author', $this->actor());
                $this->setPropertyPerReflection($entity, 'parent_uid', $transaction->newValue);
                break;
            case ForumCommentTransaction::TYPE_EDIT_TEXT:
                $this->setPropertyPerReflection($entity, 'rawText', $transaction->newValue);
                break;
            case ForumCommentTransaction::TYPE_EDIT_DELETED:
                $this->setPropertyPerReflection($entity, 'deleted', $transaction->newValue);
                break;
        }

        $entity->updateModifiedAt();
    }
}
