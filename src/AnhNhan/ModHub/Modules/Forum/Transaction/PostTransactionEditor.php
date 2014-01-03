<?php
namespace AnhNhan\ModHub\Modules\Forum\Transaction;

use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\ModHub\Modules\Forum\Storage\PostTransaction;
use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;
use AnhNhan\ModHub\Storage\Transaction\TransactionEditor;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class PostTransactionEditor extends TransactionEditor
{
    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = PostTransaction::TYPE_EDIT_POST;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                return null;
            case PostTransaction::TYPE_EDIT_POST:
                return $entity->rawText();
        }
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE: // Set new value to DISQ UID
                $uid = new \AnhNhan\ModHub\Storage\Types\UID($transaction->newValue());
                if ($uid->getType() !== "DISQ") {
                    throw new \Exception(sprintf("You have to pass in a valid DISQ UID! ('%s' given)", $transaction->newValue()));
                }
                // Fall through
            case PostTransaction::TYPE_EDIT_POST:
                return $transaction->newValue();
        }
    }

    protected function applyCustomTransactionEffects($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                // Set author field by hacking
                $reflClass = $this->em()->getClassMetadata('AnhNhan\ModHub\Modules\Forum\Storage\Post')
                    ->reflClass;
                $authorReflProp = $reflClass->getProperty('author');
                $authorReflProp->setAccessible(true);
                $authorReflProp->setValue(
                    $entity, $this->actor()
                );
                break;
            case PostTransaction::TYPE_EDIT_POST:
                $entity->setRawText($transaction->newValue());
                break;
        }

        $entity->updateModifiedAt();
    }

    protected function postApplyHook($entity, array $transactions)
    {
        // Fish for the 'create' transaction to pick the discussion uid
        $transactions = mpull($transactions, "newValue", "type");
        $disqId = idx($transactions, TransactionEntity::TYPE_CREATE);
        if (!$disqId) {
            return;
        }

        // Register the post with the discussion
        $discussion = $this->em()
            ->getRepository('AnhNhan\ModHub\Modules\Forum\Storage\Discussion')
            ->find($disqId);
        $editor = DiscussionTransactionEditor::create($this->em())
            ->setActor($this->actor())
            ->setEntity($discussion)
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_ADD_POST, $entity->uid())
            )
        ;

        $editor->apply();
    }
}
