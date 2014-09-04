<?php
namespace AnhNhan\Converge\Modules\Forum\Transaction;

use AnhNhan\Converge\Modules\Forum\Storage\DiscussionTag;
use AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;

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
        $types[] = DiscussionTransaction::TYPE_ADD_COMMENT;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
            case DiscussionTransaction::TYPE_ADD_TAG:
            case DiscussionTransaction::TYPE_ADD_POST:
            case DiscussionTransaction::TYPE_ADD_COMMENT:
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
            case DiscussionTransaction::TYPE_ADD_COMMENT: // Set new value to UID
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
                $this->setPropertyPerReflection($entity, 'author', $this->actor());
                break;
            case DiscussionTransaction::TYPE_EDIT_LABEL:
                $entity->setLabel($transaction->newValue());
                break;
            case DiscussionTransaction::TYPE_EDIT_TEXT:
                $entity->text($transaction->newValue());
                break;
            case DiscussionTransaction::TYPE_ADD_TAG:
                // See post-apply hook
                break;
            case DiscussionTransaction::TYPE_REMOVE_TAG:
                $tag = null;
                foreach ($entity->tags() ?: array() as $tt) {
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
            case DiscussionTransaction::TYPE_ADD_COMMENT:
                // <do nothing (handled by controller)>
                break;
        }

        $entity->updateLastActivity();
    }

    public function postApplyHook($entity, array $transactions)
    {
        $grpd_xacts = mgroup($transactions, "type");
        $dtag_add_xacts = idx($grpd_xacts, DiscussionTransaction::TYPE_ADD_TAG, []);

        foreach ($dtag_add_xacts as $xact)
        {
            $dTag = new DiscussionTag($entity, $xact->newValue());
            $this->em()->persist($dTag);
            if ($tags = $entity->tags()) {
                $tags->add($dTag);
            }
        }

        $this->em()->persist($entity);
        $this->finalFlush();
    }
}
