<?php
namespace AnhNhan\Converge\Modules\Task\Storage;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 *
 * @Entity
 * @Table
 */
class TaskBlocker extends TaskRelation
{
    /**
     * @ManyToOne(targetEntity="Task", fetch="EAGER")
     * @var Task
     */
    protected $blockingTask;

    public function blockingTask()
    {
        return $this->blockingTask;
    }

    protected function getChildTask()
    {
        return $this->blockingTask;
    }

    protected function setChildTask(Task $task)
    {
        $this->blockingTask = $task;
    }
}
