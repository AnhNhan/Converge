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
class DiscussionTransaction extends TransactionEntity
{
    const TYPE_EDIT_LABEL = "disq.edit.label";
    const TYPE_EDIT_TEXT  = "disq.edit.text";
    const TYPE_ADD_TAG    = "disq.add.tag";
    const TYPE_REMOVE_TAG = "disq.remove.tag";
    const TYPE_ADD_POST   = "disq.add.post";

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
