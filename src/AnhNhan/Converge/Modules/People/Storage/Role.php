<?php
namespace AnhNhan\Converge\Modules\People\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table
 * @Cache
 */
class Role extends EntityDefinition implements TransactionAwareEntityInterface
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
     * @Column(type="string", unique=true)
     */
    private $name;

    /**
     * @Column(type="string", unique=true)
     */
    private $label;

    /**
     * @Column(type="text", nullable=true)
     */
    private $description;

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
     * @OneToMany(targetEntity="RoleTransaction", mappedBy="object", fetch="LAZY")
     */
    private $xacts;

    public function __construct()
    {
        $this->createdAt  = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    /**
     * @internal For tests only
     */
    public static function initializeWithName($name)
    {
        $role = new static;
        $role->name = $name;
        return $role;
    }

    public function uid()
    {
        return $this->uid;
    }

    /**
     * The name of the role, e.g. "ROLE_ADMIN"
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    public function label()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        if (empty($label)) {
            throw new \InvalidArgumentException("Label can't be empty!");
        }
        $this->label = $label;
        return $this;
    }

    public function description()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        if (!$description) {
            $description = null;
        }
        $this->description = $description;
        return $this;
    }

    public function updateModifiedAt()
    {
        $this->modifiedAt = new \DateTime;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions()
    {
        return $this->xacts;
    }

    public function getUIDType()
    {
        return "ROLE";
    }
}
