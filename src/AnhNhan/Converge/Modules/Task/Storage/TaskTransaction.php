<?php
namespace AnhNhan\Converge\Modules\Task\Storage;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table
 * @Cache
 */
class TaskTransaction extends TransactionEntity
{
    const TYPE_EDIT_LABEL     = "task.edit.label";
    const TYPE_EDIT_DESC      = "task.edit.description";
    const TYPE_EDIT_STATUS    = "task.edit.status";
    const TYPE_EDIT_PRIORITY  = "task.edit.priority";
    const TYPE_EDIT_COMPLETED = "task.edit.completed";

    /**
     * @ManyToOne(targetEntity="Task", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    /**
     * @return Role
     */
    public function task()
    {
        return $this->object;
    }

    protected function getUIDSubType()
    {
        return "TASK";
    }
}
