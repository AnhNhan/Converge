<?php
namespace AnhNhan\Converge\Modules\Task\Storage;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table(indexes={
 *   @Index(name="idx_object", columns={"id", "object_id"})
 * })
 * @Cache
 */
class TaskStatusTransaction extends TransactionEntity
{
    const TYPE_EDIT_LABEL = "task.status.edit.label";
    const TYPE_EDIT_COLOR = "task.status.edit.color";

    /**
     * @ManyToOne(targetEntity="TaskStatus", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    /**
     * @return Role
     */
    public function task_status()
    {
        return $this->object;
    }

    protected function getUIDSubType()
    {
        return "TSTA";
    }
}
