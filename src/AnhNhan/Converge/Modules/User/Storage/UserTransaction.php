<?php
namespace AnhNhan\Converge\Modules\User\Storage;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table(indexes={
 *   @Index(name="idx_object", columns={"id", "object_id"})
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
