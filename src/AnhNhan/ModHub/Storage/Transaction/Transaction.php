<?php
namespace AnhNhan\ModHub\Storage\Transaction;

use AnhNhan\ModHub\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class Transaction
{
    abstract public function applyTransaction($entity, $xactType, $value);

    /**
     * @return array
     */
    abstract public function getTransactionTypes();
}
