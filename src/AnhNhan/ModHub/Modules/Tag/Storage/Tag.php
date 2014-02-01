<?php
namespace AnhNhan\ModHub\Modules\Tag\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table(indexes={
 *   @Index(name="label_sorting", columns={"label", "displayOrder"})
 * })
 */
class Tag extends EntityDefinition implements TransactionAwareEntityInterface
{
    /**
     * @Id
     * @Column(type="string")
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="AnhNhan\ModHub\Storage\Doctrine\UIDGenerator")
     */
    private $id;

    /**
     * @Column(type="string", unique=true)
     */
    private $label;

    /**
     * @Column(type="integer")
     */
    private $displayOrder = 0;

    /**
     * @Column(type="string", nullable=true)
     */
    private $color;

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
     * @OneToMany(targetEntity="TagTransaction", mappedBy="object", fetch="LAZY")
     * @OrderBy({"createdAt"="ASC"})
     */
    private $xacts;

    public function __construct() {
        $this->createdAt  = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    public function uid()
    {
        return $this->id;
    }

    public function label()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function displayOrder()
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder($displayOrder = 0)
    {
        if (
            (empty($displayOrder) && $displayOrder !== 0 && $displayOrder !== '0')
            || !is_numeric($displayOrder)
        ) {
            throw new \InvalidArgumentException;
        }
        $this->displayOrder = $displayOrder;
        return $this;
    }

    public function color()
    {
        return $this->color;
    }

    public function setColor($color = null)
    {
        if (empty($color)) {
            $color = null;
        }
        $this->color = $color;
        return $this;
    }

    public function description()
    {
        return $this->description;
    }

    public function setDescription($description = null)
    {
        $description = trim($description);
        if (empty($description)) {
            $description = null;
        }
        $this->description = $description;
        return $this;
    }

    public function createdAt()
    {
        return $this->createdAt;
    }

    public function modifiedAt()
    {
        return $this->modifiedAt;
    }

    public function updateModifiedDate()
    {
        $this->modifiedAt = new \DateTime;
    }

    public function getUIDType()
    {
        return "TTAG";
    }

    public function toDictionary()
    {
        return array(
            "uid"          => $this->uid(),
            "label"        => $this->label(),
            "color"        => $this->color(),
            "displayOrder" => $this->displayOrder(),
            "description"  => $this->description(),
        );
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions()
    {
        return $this->xacts;
    }
}
