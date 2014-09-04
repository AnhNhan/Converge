<?php
namespace AnhNhan\Converge\Modules\Forum\Storage;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Cache
 * @Table(indexes={
 *   @Index(name="idx_object", columns={"id", "object_id"})
 * })
 */
class PostTransaction extends TransactionEntity
{
    const TYPE_EDIT_POST    = "post.edit.text";
    const TYPE_EDIT_DELETED = "post.edit.delete";
    const TYPE_ADD_COMMENT  = "post.add.comment";

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
