<?php
namespace AnhNhan\Converge\Modules\Activity\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 *
 * @Entity
 * @Table(indexes={
 *   @Index(name="actor_uid", columns={"actor_uid"}),
 *   @Index(name="object_uid", columns={"object_uid"}),
 *   @Index(name="author_xact_type", columns={"actor_uid", "xact_type"}),
 *   @Index(name="xact_type", columns={"xact_type"})
 * })
 * @Cache
 */
class RecordedActivity extends EntityDefinition
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
     * @Column(type="string")
     */
    public $object_uid;
    /**
     * @Column(type="string")
     */
    public $object_label;
    /**
     * @Column(type="string", nullable=true)
     */
    public $object_link;

    /**
     * @Column(type="string")
     */
    public $actor_uid;

    /**
     * @Column(type="string")
     */
    public $xact_uid;
    /**
     * @Column(type="string")
     */
    public $xact_type;
    /**
     * @Column(type="text", nullable=true)
     */
    public $xact_contents;

    /**
     * @Column(type="text", nullable=true)
     */
    public $metadata;

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

    public function __construct() {
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    public function uid()
    {
        return $this->uid;
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
        return "ACTI";
    }
}
