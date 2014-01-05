<?php
namespace AnhNhan\ModHub\Modules\Forum\Storage;

use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table
 */
class PostTransaction extends TransactionEntity
{
    const TYPE_EDIT_POST    = "post.edit.text";
    const TYPE_EDIT_DELETED = "post.edit.delete";

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
