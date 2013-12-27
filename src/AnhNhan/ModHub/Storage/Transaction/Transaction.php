<?php
namespace AnhNhan\ModHub\Storage\Transaction;

use AnhNhan\ModHub\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class Transaction
{
    /**
     * @return string
     */
    abstract public function getEntityClass();

    /**
     * @return array
     */
    abstract public function getTransactionTypes();
}
