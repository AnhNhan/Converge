<?php
namespace AnhNhan\ModHub\Storage\Transaction;

/**
 * Declares that an entity is aware about transaction logging and is ready to
 * help us to use it.
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
interface TransactionAwareEntityInterface
{
    /**
     * Returns the name of the entity class used to store transactions.
     *
     * @return string
     */
    public function getTransactionEntityClass();

    /**
     * Returns the name of the class with more metadata about the transactions
     * possible with this entity
     *
     * @return string
     */
    public function getTransactionClass();

    /**
     * Returns the collection of transactions associated with this entity
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions();
}
