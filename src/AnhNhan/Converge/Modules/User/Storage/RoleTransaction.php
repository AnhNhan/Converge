<?php
namespace AnhNhan\Converge\Modules\User\Storage;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table(indexes={
 *   @Index(name="insertion_order", columns={"createdAt"})
 * })
 */
class RoleTransaction extends TransactionEntity
{
    const TYPE_EDIT_LABEL = "role.edit.label";
    const TYPE_EDIT_DESC  = "role.edit.description";

    /**
     * @ManyToOne(targetEntity="Role", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    /**
     * @return Role
     */
    public function role()
    {
        return $this->object;
    }

    protected function getUIDSubType()
    {
        return "ROLE";
    }
}
