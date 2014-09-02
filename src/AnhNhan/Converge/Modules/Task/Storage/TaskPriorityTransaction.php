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
class TaskPriorityTransaction extends TransactionEntity
{
    const TYPE_EDIT_LABEL = "task.priority.edit.label";
    const TYPE_EDIT_ORDER = "task.priority.edit.order";
    const TYPE_EDIT_COLOR = "task.priority.edit.color";

    /**
     * @ManyToOne(targetEntity="TaskPriority", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    /**
     * @return Role
     */
    public function task_priority()
    {
        return $this->object;
    }

    protected function getUIDSubType()
    {
        return "TPRI";
    }
}
