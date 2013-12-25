<?php
namespace AnhNhan\ModHub\Storage\Transaction;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
interface TransactionAwareEntityInterface
{
    public function getTransactionEntityClass();

    public function getTransactionClass();
}
