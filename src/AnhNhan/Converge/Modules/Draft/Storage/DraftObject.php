<?php
namespace AnhNhan\Converge\Modules\Draft\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table
 */
class DraftObject extends EntityDefinition
{
    /**
     * @Id
     * @Column(type="string")
     */
    public $user_uid;

    /**
     * @Id
     * @Column(type="string")
     */
    public $object_uid;

    /**
     * @Column(type="text")
     */
    public $contents;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    public $modifiedAt;

    public function __construct() {
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    public function updateModifiedAt()
    {
        $this->modifiedAt = new \DateTime;
        return $this;
    }
}
