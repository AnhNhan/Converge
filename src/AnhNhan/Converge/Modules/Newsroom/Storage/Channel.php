<?php
namespace AnhNhan\Converge\Modules\Newsroom\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table
 */
class Channel extends EntityDefinition implements TransactionAwareEntityInterface
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
    private $label;

    /**
     * @Column(type="string", unique=true)
     */
    private $slug;

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
     * @OneToMany(targetEntity="ChannelTransaction", mappedBy="object", fetch="LAZY")
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

    public function slug()
    {
        return $this->slug;
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
        return "CHAN";
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions()
    {
        return $this->xacts;
    }
}
