<?php
namespace AnhNhan\ModHub\Modules\Forum\Storage;

use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table
 */
class PostTransaction extends TransactionEntity
{
    /**
     * @ManyToOne(targetEntity="Post", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    /**
     * @return Post
     */
    public function post()
    {
        return $this->object;
    }

    protected function getUIDSubType()
    {
        return "POST";
    }
}
