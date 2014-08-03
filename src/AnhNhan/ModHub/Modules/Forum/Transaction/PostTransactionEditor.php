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
        $types[] = PostTransaction::TYPE_EDIT_DELETED;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                return null;
            case PostTransaction::TYPE_EDIT_DELETED:
                return $entity->deleted();
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
            case PostTransaction::TYPE_EDIT_DELETED:
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
            case PostTransaction::TYPE_EDIT_DELETED:
                // Set deleted field by hacking
                $reflClass = $this->em()->getClassMetadata('AnhNhan\ModHub\Modules\Forum\Storage\Post')
                    ->reflClass;
                $authorReflProp = $reflClass->getProperty('deleted');
                $authorReflProp->setAccessible(true);
                $authorReflProp->setValue(
                    $entity, (Boolean) $transaction->newValue()
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
        // This is mostly a check whether we are creating or not
        $transactions = mpull($transactions, "newValue", "type");
        $disqId = idx($transactions, TransactionEntity::TYPE_CREATE);
        if (!$disqId) {
            return;
        }

        $disq = $entity->parentDisq();
        if (!$disq) {
            throw new \Exception(
                "The post entity has no attached discussion!You can attach one " .
                "during instantiation by calling `Post::initializeForDiscussion($disq)`."
            );
        }
        if ($disq->uid() !== $disqId) {
            throw new \Exception("WTF?? Please check your code. Thoroughly.");
        }

        if (!$entity->uid()) {
            // We apparently didn't flush the new post yet. Flush it so we get
            // a UID
            $this->em()->flush();
        }

        // Register the post with the discussion
        $editor = DiscussionTransactionEditor::create($this->em())
            ->setActor($this->actor())
            ->setFlushBehaviour($this->flushBehaviour())
            ->setEntity($disq)
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_ADD_POST, $entity->uid())
            )
        ;

        $editor->apply();
    }
}
