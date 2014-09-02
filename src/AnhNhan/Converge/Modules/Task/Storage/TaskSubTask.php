<?php
namespace AnhNhan\Converge\Modules\Task\Storage;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 *
 * @Entity
 * @Table
 */
class TaskSubTask extends TaskRelation
{
    /**
     * @ManyToOne(targetEntity="Task", fetch="EAGER")
     * @var Task
     */
    protected $subTask;

    public function subTask()
    {
        return $this->subTask;
    }

    protected function getChildTask()
    {
        return $this->subTask;
    }

    protected function setChildTask(Task $task)
    {
        $this->subTask = $task;
    }
}
