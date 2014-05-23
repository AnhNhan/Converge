<?php
namespace AnhNhan\ModHub\Storage\Transaction;

use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Types\UID;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class TransactionEntity extends EntityDefinition
{
    const TYPE_CREATE = "entity.create";

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string", unique=true)
     */
    protected $uid;

    /**
     * @Column(type="string")
     */
    protected $actor;

    /**
     * NOTE: May be overridden by subclasses
     *
     * @Column(type="string")
     */
    protected $object;

    /**
     * @Column(type="string")
     */
    protected $type;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $oldValue;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $newValue;

    /**
     * @Column(type="text")
     */
    protected $metadata;

    /**
     * @Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @Column(type="datetime")
     */
    protected $modifiedAt;

    public function __construct(array $metadata = null)
    {
        $this->metadata = json_encode($metadata);
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    /**
     * Convenience method for fast chainability
     */
    public static function create($type, $newValue = null, array $metadata = null)
    {
        return id(new static($metadata))
            ->setType($type)
            ->setNewValue($newValue)
        ;
    }

    public function uid()
    {
        return $this->uid;
    }

    public function actorId()
    {
        return $this->actor;
    }

    public function setActorId($id)
    {
        $this->actor = $id;
        return $this;
    }

    public function object()
    {
        return $this->object;
    }

    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }

    public function type()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function oldValue()
    {
        return $this->oldValue;
    }

    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;
        return $this;
    }

    public function newValue()
    {
        return $this->newValue;
    }

    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;
        return $this;
    }

    /**
     * Returns the JSON-decoded transaction metadata
     *
     * @return array
     */
    public function metadata()
    {
        return (array) json_decode($this->metadata);
    }

    /**
     * @return \DateTime
     */
    public function createdAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function modifiedAt()
    {
        return $this->modifiedAt;
    }

    abstract protected function getUIDSubType();

    final public function getUIDType()
    {
        return sprintf("XACT-%s", $this->getUIDSubType());
    }
}
