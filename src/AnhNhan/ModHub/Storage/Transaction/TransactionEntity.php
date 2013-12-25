<?php
namespace AnhNhan\ModHub\Storage\Transaction;

use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Types\UID;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class TransactionEntity extends EntityDefinition
{
    /**
     * @Id
     * @Column(type="string")
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="AnhNhan\ModHub\Storage\Doctrine\UIDGenerator")
     */
    protected $id;

    /**
     * @Column(type="string")
     */
    protected $author;

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
     * @Column(type="text")
     */
    protected $oldValue;

    /**
     * @Column(type="text")
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

    public function __construct($author, $object, $type, $oldValue, $newValue, array $metadata = null)
    {
        if (!($object instanceof EntityDefinition) || ($object instanceof TransactionEntity)) {
            throw new \InvalidArgumentException("Invalid object type");
        }
        UID::checkValidity($author);

        $this->author   = $author;
        $this->object   = $object;
        $this->type     = $type;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
        $this->metadata = json_encode($metadata);
    }

    public function uid()
    {
        return $this->id;
    }

    public function authorId()
    {
        return $this->author;
    }

    public function object()
    {
        return $this->object;
    }

    public function type()
    {
        return $this->type;
    }

    public function oldValue()
    {
        return $this->oldValue;
    }

    public function newValue()
    {
        return $this->newValue;
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
        return sprintf("%s-XACT", $this->getUIDSubType());
    }
}
