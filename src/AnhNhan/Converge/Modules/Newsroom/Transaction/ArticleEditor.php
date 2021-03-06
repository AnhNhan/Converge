<?php
namespace AnhNhan\Converge\Modules\Newsroom\Transaction;

use AnhNhan\Converge\Modules\Newsroom\Storage\ArticleAuthor;
use AnhNhan\Converge\Modules\Newsroom\Storage\ArticleTag;
use AnhNhan\Converge\Modules\Newsroom\Storage\ArticleTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class ArticleEditor extends TransactionEditor
{
    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = ArticleTransaction::TYPE_EDIT_TITLE;
        $types[] = ArticleTransaction::TYPE_EDIT_CHANNEL;
        $types[] = ArticleTransaction::TYPE_EDIT_BYLINE;
        $types[] = ArticleTransaction::TYPE_ADD_AUTHOR;
        $types[] = ArticleTransaction::TYPE_DEL_AUTHOR;
        $types[] = ArticleTransaction::TYPE_ADD_TAG;
        $types[] = ArticleTransaction::TYPE_DEL_TAG;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                return null;
            case ArticleTransaction::TYPE_EDIT_TITLE:
                return $entity->title();
            case ArticleTransaction::TYPE_EDIT_BYLINE:
                return $entity->byline();
            case ArticleTransaction::TYPE_EDIT_CHANNEL:
                return $entity->channel() ? $entity->channel()->uid : null;
            case ArticleTransaction::TYPE_ADD_AUTHOR:
            case ArticleTransaction::TYPE_ADD_TAG:
                return null;
            case ArticleTransaction::TYPE_DEL_AUTHOR:
            case ArticleTransaction::TYPE_DEL_TAG:
                return $transaction->newValue;
        }
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
            case ArticleTransaction::TYPE_EDIT_TITLE:
            case ArticleTransaction::TYPE_EDIT_BYLINE:
            case ArticleTransaction::TYPE_EDIT_CHANNEL:
            case ArticleTransaction::TYPE_ADD_AUTHOR:
            case ArticleTransaction::TYPE_ADD_TAG:
                return $transaction->newValue;
            case ArticleTransaction::TYPE_DEL_AUTHOR:
            case ArticleTransaction::TYPE_DEL_TAG:
                return null;
        }
    }

    protected function applyCustomTransactionEffects($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case TransactionEntity::TYPE_CREATE:
                $this->setPropertyPerReflection($entity, 'slug', to_slug($transaction->newValue));
                break;
            case ArticleTransaction::TYPE_EDIT_TITLE:
                $this->setPropertyPerReflection($entity, 'title', $transaction->newValue);
                break;
            case ArticleTransaction::TYPE_EDIT_BYLINE:
                $this->setPropertyPerReflection($entity, 'byline', $transaction->newValue);
                break;
            case ArticleTransaction::TYPE_EDIT_CHANNEL:
                $this->setPropertyPerReflection($entity, 'channel', $transaction->newValue);
                $this->setPropertyPerReflection($transaction, 'newValue', $transaction->newValue->uid);
                break;
            case ArticleTransaction::TYPE_ADD_AUTHOR:
                $articleAuthor = new ArticleAuthor($entity, $transaction->newValue);
                $this->persistLater($articleAuthor);
                break;
            case ArticleTransaction::TYPE_DEL_AUTHOR:
                $articleAuthor = new ArticleAuthor($entity, $transaction->oldValue);
                $articleAuthor = $this->em()->merge($articleAuthor);
                $this->em()->remove($articleAuthor);
                break;
            case ArticleTransaction::TYPE_ADD_TAG:
                $articleTag = new ArticleTag($entity, $transaction->newValue);
                $this->persistLater($articleTag);
                break;
            case ArticleTransaction::TYPE_DEL_TAG:
                $articleTag = new ArticleTag($entity, $transaction->oldValue);
                $articleTag = $this->em()->merge($articleTag);
                $this->em()->remove($articleTag);
                break;
        }

        $entity->updateModifiedAt();
    }
}
