<?php
namespace AnhNhan\Converge\Modules\Newsroom\Storage;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table
 * @Cache
 */
class ChannelTransaction extends TransactionEntity
{
    const TYPE_EDIT_LABEL = "task.edit.label";

    /**
     * @ManyToOne(targetEntity="Channel", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    /**
     * @return Role
     */
    public function channel()
    {
        return $this->object;
    }

    protected function getUIDSubType()
    {
        return "CHAN";
    }
}
