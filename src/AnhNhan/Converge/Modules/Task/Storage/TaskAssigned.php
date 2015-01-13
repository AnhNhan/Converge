<?php
namespace AnhNhan\Converge\Modules\Task\Storage;

use AnhNhan\Converge\Modules\People\Storage\User;
use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Types\UID;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 *
 * @Entity
 * @Table
 * @Cache("NONSTRICT_READ_WRITE")
 */
class TaskAssigned extends EntityDefinition
{
    /**
     * @Id
     * @ManyToOne(targetEntity="Task", fetch="EAGER", inversedBy="assigned")
     * @var Task
     */
    private $task;

    /**
     * @Id
     * @Column(type="string")
     * @var string
     */
    private $user;

    /**
     * @var User
     */
    private $user_object;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $name;

    public function __construct(Task $task, $user)
    {
        $this->task = $task;
        $this->setUser($user);
    }

    public function task()
    {
        return $this->task;
    }

    public function taskId()
    {
        return $this->task->uid;
    }

    public function user()
    {
        return $this->user_object;
    }

    public function userId()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        if (is_object($user) && $user instanceof User) {
            $this->user = $user->uid;
            $this->user_object = $user;
        } else {
            // We only received a UID string
            UID::checkValidity($user);
            $this->user = $user;
        }
    }
}
