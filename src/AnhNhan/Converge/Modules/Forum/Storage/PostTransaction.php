<?php
namespace AnhNhan\Converge\Modules\Forum\Storage;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Cache
 * @Table(indexes={
 *   @Index(name="insertion_order", columns={"createdAt"})
 * })
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
