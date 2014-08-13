<?php
namespace AnhNhan\Converge\Storage\Transaction;

/**
 * Declares that an entity is aware about transaction logging and is ready to
 * help us to use it.
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
interface TransactionAwareEntityInterface
{
    /**
     * Returns the UID of this entity. Required for all transaction aware entities.
     *
     * @return string
     */
    public function uid();

    /**
     * Returns the collection of transactions associated with this entity
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions();
}
