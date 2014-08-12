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
    const TYPE_ADD_ROLE      = "user.add.role";
    const TYPE_REMOVE_ROLE   = "user.remove.role";
    const TYPE_ADD_EMAIL      = "user.add.email";
    const TYPE_REMOVE_EMAIL   = "user.remove.email";

    /**
     * @ManyToOne(targetEntity="User", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    /**
     * @return User
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
