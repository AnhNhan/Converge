<?php
namespace AnhNhan\ModHub\Modules\Forum\Storage;

use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table
 */
class DiscussionTransaction extends TransactionEntity
{
    /**
     * @ManyToOne(targetEntity="Discussion", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    /**
     * @return Discussion
     */
    public function discussion()
    {
        return $this->object;
    }

    protected function getUIDSubType()
    {
        return "DISQ";
    }
}
