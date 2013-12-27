<?php
namespace AnhNhan\ModHub\Modules\User\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;

use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table
 */
class Role extends EntityDefinition implements RoleInterface
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
    private $name;

    /**
     * @Column(type="string", unique=true)
     */
    private $label;

    /**
     * @Column(type="text", nullable=true)
     */
    private $description;

    public function __construct($name, $label, $description = null)
    {
        $this->name = $name;
        $this->label($label);
        $this->description($description);
    }

    public function uid()
    {
        return $this->id;
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

    public function label($label = null)
    {
        if ($label === null) {
            return $this->label;
        } else {
            if (empty($label)) {
                throw new \InvalidArgumentException("Label can't be empty!");
            }
            $this->label = $label;
            return $this;
        }
    }

    public function description($description = null)
    {
        if ($description === null) {
            return $this->description;
        } else {
            if (!$description) {
                $description = null;
            }
            $this->description = $description;
            return $this;
        }
    }

    public function getRole()
    {
        return $this->name();
    }

    public function getUIDType()
    {
        return "ROLE";
    }
}
