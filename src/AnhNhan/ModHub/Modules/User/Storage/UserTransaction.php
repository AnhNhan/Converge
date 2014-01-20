<?php
namespace AnhNhan\ModHub\Modules\User\Storage;

use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table(indexes={
 *   @Index(name="insertion_order", columns={"createdAt"})
 * })
 */
class UserTransaction extends TransactionEntity
{
    const TYPE_EDIT_PASSWORD = "user.edit.password";
    const TYPE_EDIT_DISPNAME = "user.edit.dispname";
    const TYPE_EDIT_IMAGE    = "user.edit.image";
    const TYPE_ADD_ROLE      = "user.add.role";
    const TYPE_REMOVE_ROLE   = "user.remove.role";

    /**
     * @ManyToOne(targetEntity="User", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    /**
     * @return Role
     */
    public function user()
    {
        return $this->object;
    }

    protected function getUIDSubType()
    {
        return "USER";
    }
}
