<?php
namespace AnhNhan\ModHub\Modules\Tag\Storage;

use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table
 */
class TagTransaction extends TransactionEntity
{
    const TYPE_EDIT_LABEL = "tag.edit.label";
    const TYPE_EDIT_DESC  = "tag.edit.description";
    const TYPE_EDIT_COLOR = "tag.edit.color";
    const TYPE_EDIT_ORDER = "tag.edit.displayorder";

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

    public function getTransactionTypes()
    {
        return array(
            static::TYPE_EDIT_LABEL,
            static::TYPE_EDIT_DESC,
            static::TYPE_EDIT_COLOR,
            static::TYPE_EDIT_ORDER,
        );
    }
}
