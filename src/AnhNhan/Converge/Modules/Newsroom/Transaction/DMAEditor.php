<?php
namespace AnhNhan\Converge\Modules\Newsroom\Transaction;

use AnhNhan\Converge\Modules\Newsroom\Storage\DMArticleTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DMAEditor extends ArticleEditor
{
    public function getTransactionTypes()
    {
        $types = parent::getTransactionTypes();

        $types[] = DMArticleTransaction::TYPE_EDIT_TEXT;
        $types[] = DMArticleTransaction::TYPE_EDIT_SETTING;

        return $types;
    }

    protected function getCustomTransactionOldValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case DMArticleTransaction::TYPE_EDIT_TEXT:
                return $entity->rawText();
            case DMArticleTransaction::TYPE_EDIT_SETTING:
                return $entity->settings();
        }

        return parent::getCustomTransactionOldValue($entity, $transaction);
    }

    protected function getCustomTransactionNewValue($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case DMArticleTransaction::TYPE_EDIT_TEXT:
            case DMArticleTransaction::TYPE_EDIT_SETTING:
                return $transaction->newValue();
        }

        return parent::getCustomTransactionNewValue($entity, $transaction);
    }

    protected function applyCustomTransactionEffects($entity, TransactionEntity $transaction)
    {
        switch ($transaction->type()) {
            case DMArticleTransaction::TYPE_EDIT_TEXT:
                $this->setPropertyPerReflection($entity, 'rawText', $transaction->newValue);
                break;
            case DMArticleTransaction::TYPE_EDIT_SETTING:
                $this->setPropertyPerReflection($entity, 'settings', $transaction->newValue);
                $this->setPropertyPerReflection($transaction, 'oldValue', json_encode($transaction->oldValue));
                $this->setPropertyPerReflection($transaction, 'newValue', json_encode($transaction->newValue));
                break;
        }

        return parent::applyCustomTransactionEffects($entity, $transaction);
    }
}
