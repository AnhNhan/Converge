<?php
namespace AnhNhan\ModHub\Modules\Forum\Transaction;

use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;
use AnhNhan\ModHub\Storage\Transaction\TransactionEditor;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionTransactionEditor extends TransactionEditor
{
    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = DiscussionTransaction::TYPE_EDIT_LABEL;
        $types[] = DiscussionTransaction::TYPE_EDIT_TEXT;
        $types[] = DiscussionTransaction::TYPE_ADD_TAG;
        $types[] = DiscussionTransaction::TYPE_REMOVE_TAG;
        $types[] = DiscussionTransaction::TYPE_ADD_POST;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
            case DiscussionTransaction::TYPE_ADD_TAG:
            case DiscussionTransaction::TYPE_ADD_POST:
                return null;
            case DiscussionTransaction::TYPE_EDIT_LABEL:
                return $entity->label();
            case DiscussionTransaction::TYPE_EDIT_TEXT:
                return $entity->text();
            case DiscussionTransaction::TYPE_REMOVE_TAG: // Set old value to UID
                return $transaction->newValue();
        }
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case DiscussionTransaction::TYPE_EDIT_LABEL:
            case DiscussionTransaction::TYPE_EDIT_TEXT:
            case DiscussionTransaction::TYPE_ADD_TAG:  // Set new value to UID
            case DiscussionTransaction::TYPE_ADD_POST: // Set new value to UID
                return $transaction->newValue();
            case TransactionEntity::TYPE_CREATE:
            case DiscussionTransaction::TYPE_REMOVE_TAG:
                return null;
        }
    }

    protected function applyCustomTransactionEffects($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                // Set author field by hacking
                $discussionReflProp = $this->em()->getClassMetadata('AnhNhan\ModHub\Modules\Forum\Storage\Discussion')
                    ->reflClass->getProperty('author');
                $discussionReflProp->setAccessible(true);
                $discussionReflProp->setValue(
                    $entity, $this->actor()
                );
                break;
            case DiscussionTransaction::TYPE_EDIT_LABEL:
                $entity->setLabel($transaction->newValue());
                break;
            case DiscussionTransaction::TYPE_EDIT_TEXT:
                $entity->text($transaction->newValue());
                break;
            case DiscussionTransaction::TYPE_ADD_TAG:
                $dTag = new DiscussionTag($entity, $transaction->newValue());
                $this->persistLater($dTag);
                break;
            case DiscussionTransaction::TYPE_REMOVE_TAG:
                $tag = null;
                foreach ($entity->tags() as $tt) {
                    if ($tt->tagId() == $transaction->oldValue()) {
                        $tag = $tt;
                    }
                }
                if (!$tag) {
                    throw new \Exception(
                        "Tag " . $transaction->oldValue() .
                        " can't be detached from " . $entity->uid() .
                        ", not attached!"
                    );
                }
                $this->em()->remove($tag);
                break;
            case DiscussionTransaction::TYPE_ADD_POST:
                // <do nothing (handled by controller)>
                break;
        }

        $entity->updateLastActivity();
    }
}
