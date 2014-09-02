<?php
namespace AnhNhan\Converge\Modules\Task\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 *
 * @Entity
 * @Table
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="relation_type", type="string")
 */
abstract class TaskRelation extends EntityDefinition
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Task", fetch="EAGER")
     * @var Task
     */
    protected $parentTask;

    public function __construct(Task $parent, Task $child)
    {
        if ($parent == $child)
        {
            throw new \LogicException("Can't have task relating to itself.");
        }

        $this->parentTask = $parent;
        $this->setChildTask($child);
    }

    public function parentTask()
    {
        return $this->parentTask;
    }

    abstract protected function setChildTask(Task $child);
    abstract protected function getChildTask();

    public function serializeForXAct()
    {
        return json_encode([
            'parent' => $this->parentTask->uid,
            'child' => $this->getChildTask->uid,
            'type' => get_class($this),
        ]);
    }
}
