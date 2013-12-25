<?php
namespace AnhNhan\ModHub\Modules\Tag\Storage;

use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table
 */
class TagTransaction extends TransactionEntity
{
    /**
     * @ManyToOne(targetEntity="Tag", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    /**
     * @return Tag
     */
    public function tag()
    {
        return $this->object;
    }

    protected function getUIDSubType()
    {
        return "TTAG";
    }
}
