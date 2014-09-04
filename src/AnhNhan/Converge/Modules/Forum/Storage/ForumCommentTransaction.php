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
class ForumCommentTransaction extends TransactionEntity
{
    const TYPE_EDIT_TEXT    = "forum_comment.edit.text";
    const TYPE_EDIT_DELETED = "forum_comment.edit.deleted";

    /**
     * @ManyToOne(targetEntity="ForumComment", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    protected function getUIDSubType()
    {
        return "FCMT";
    }
}
