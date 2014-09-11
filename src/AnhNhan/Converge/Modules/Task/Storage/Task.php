<?php
namespace AnhNhan\Converge\Modules\Task\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 *
 * @Entity
 * @Table(indexes={
 *   @Index(name="idx_closed", columns={"id", "closed"}),
 *   @Index(name="closed_flag", columns={"closed"})
 * })
 */
class Task extends EntityDefinition implements TransactionAwareEntityInterface
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", unique=true)
     */
    private $uid;

    /**
     * Original author.
     *
     * @Column(type="string")
     * @var string
     */
    private $author;

    /**
     * @var \AnhNhan\Converge\Modules\User\Storage\User
     */
    private $author_object;

    /**
     * Current assigned user(s), may be nobody.
     *
     * @OneToMany(targetEntity="TaskAssigned", fetch="EAGER", mappedBy="task")
     * @var \Doctrine\ORM\PersistentCollection
     */
    private $assigned;

    /**
     * @Column(type="string")
     */
    private $label;

    /**
     * @Column(type="string", unique=true)
     */
    private $label_canonical;

    /**
     * @ManyToOne(targetEntity="TaskStatus", fetch="EAGER")
     * @Cache("NONSTRICT_READ_WRITE")
     * @var TaskStatus
     */
    private $status;

    /**
     * @ManyToOne(targetEntity="TaskPriority", fetch="EAGER")
     * @Cache("NONSTRICT_READ_WRITE")
     * @var TaskPriority
     */
    private $priority;

    /**
     * @Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @Column(type="boolean")
     */
    private $closed = false;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @OneToMany(targetEntity="TaskTag", fetch="EAGER", mappedBy="task")
     * @Cache("NONSTRICT_READ_WRITE")
     */
    private $tags;

    /**
     * @OneToMany(targetEntity="TaskBlocker", fetch="LAZY", mappedBy="parentTask")
     */
    private $blockedBy;

    /**
     * @OneToMany(targetEntity="TaskBlocker", fetch="LAZY", mappedBy="blockingTask")
     */
    private $blockedTasks;

    /**
     * @OneToMany(targetEntity="TaskSubTask", fetch="LAZY", mappedBy="parentTask")
     */
    private $subTasks;

    /**
     * @OneToMany(targetEntity="TaskSubTask", fetch="LAZY", mappedBy="subTask")
     */
    private $parentTasks;

    /**
     * @OneToMany(targetEntity="TaskTransaction", mappedBy="object", fetch="LAZY")
     * @var \Doctrine\ORM\PersistentCollection
     */
    private $xacts;

    public function __construct() {
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    public function uid()
    {
        return $this->uid;
    }

    public function label()
    {
        return $this->label;
    }

    public function label_canonical()
    {
        return $this->label_canonical;
    }

    public function authorId()
    {
        return $this->author;
    }

    public function author()
    {
        return $this->author_object;
    }

    public function setAuthor(\AnhNhan\Converge\Modules\User\Storage\User $author_object)
    {
        $this->author_object = $author_object;
        return $this;
    }

    public function assignedIds()
    {
        return mpull($this->assigned->toArray(), 'userId');
    }

    public function assigned()
    {
        return $this->assigned ? $this->assigned->toArray() : [];
    }

    public function description()
    {
        return $this->description;
    }

    public function tags()
    {
        return $this->tags;
    }

    public function blockedTasks()
    {
        return $this->blockedTasks;
    }

    public function blockedBy()
    {
        return $this->blockedBy;
    }

    public function subTasks()
    {
        return $this->subTasks;
    }

    public function parentTasks()
    {
        return $this->parentTasks;
    }

    /**
     * @return TaskPriority
     */
    public function priority()
    {
        return $this->priority;
    }

    /**
     * @return TaskStatus
     */
    public function status()
    {
        return $this->status;
    }

    public function closed()
    {
        return $this->closed;
    }

    public function createdAt()
    {
        return $this->createdAt;
    }

    public function modifiedAt()
    {
        return $this->modifiedAt;
    }

    public function updateModifiedAt()
    {
        $this->modifiedAt = new \DateTime;
        return $this;
    }

    public function getUIDType()
    {
        return 'TASK';
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions()
    {
        return $this->xacts;
    }
}
