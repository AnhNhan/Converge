<?php
namespace AnhNhan\Converge\Modules\Task\Storage;

use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Types\UID;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Cache("NONSTRICT_READ_WRITE")
 * @Table
 */
class TaskTag extends EntityDefinition
{
    /**
     * @Id
     * @ManyToOne(targetEntity="Task", fetch="EAGER", inversedBy="tags")
     * @var Task
     */
    private $task;

    /**
     * @Id
     * @Column(type="string")
     *
     * @var string
     */
    private $tag_uid;

    /**
     * @var Tag
     */
    private $t_obj;

    /**
     * @Column(type="float")
     * @var float
     */
    public $strength = 1.0;

    public function __construct(Task $task, $tag)
    {
        $this->task = $task;
        if (is_object($tag)) {
            $this->tag_uid = $tag->uid();
            $this->t_obj = $tag;
        } else {
            // We only received a UID string
            UID::checkValidity($tag);
            $this->tag_uid = $tag;
        }
    }

    public function task()
    {
        return $this->task;
    }

    public function taskId()
    {
        return $this->task->uid();
    }

    public function tag()
    {
        if (!$this->t_obj) {
            throw new \Exception("This object hasn't been initialized with a tag yet!");
        }
        return $this->t_obj;
    }

    public function setTag(Tag $tag)
    {
        if ($tag->uid !== $this->tag_uid) {
            throw new \InvalidArgumentException("UIDs do not match!");
        }
        $this->t_obj = $tag;
        return $this;
    }

    public function tagId()
    {
        return $this->tag_uid;
    }
}
