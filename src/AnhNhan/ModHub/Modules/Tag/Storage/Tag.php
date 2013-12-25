<?php
namespace AnhNhan\ModHub\Modules\Tag\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table @OrderBy(value="displayOrder")
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
     * @OneToMany(targetEntity="TagTransaction", mappedBy="object", fetch="LAZY")
     */
    private $xacts = array();

    public function __construct($label, $color = null, $description = null)
    {
        $this->label = $label;
        $this->color = $color;
        $this->description = $description;
    }

    public function uid()
    {
        return $this->id;
    }

    public function label()
    {
        return $this->label;
    }

    public function displayOrder($displayOrder = null)
    {
        if ($displayOrder === null) {
            return $this->displayOrder;
        } else {
            if (empty($displayOrder) || !is_numeric($displayOrder)) {
                throw new \InvalidArgumentException;
            }
            $this->displayOrder = $displayOrder;
            return $this;
        }
    }

    public function color($color = null)
    {
        if ($color === null) {
            return $this->color;
        } else {
            $this->color = $color;
            return $this;
        }
    }

    public function description($description = null)
    {
        if ($description === null) {
            return $this->description;
        } else {
            $description = trim($description);
            if (empty($description)) {
                $description = null;
            }
            $this->description = $description;
            return $this;
        }
    }

    public function getUIDType()
    {
        return "TTAG";
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions()
    {
        return $this->xacts;
    }

    public function getTransactionEntityClass()
    {
        return 'AnhNhan\ModHub\Modules\Tag\Storage\TagTransaction';
    }

    public function getTransactionClass()
    {
        return 'AnhNhan\ModHub\Modules\Tag\Transaction\TagTransaction';
    }
}
