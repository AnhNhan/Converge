<?php
namespace AnhNhan\ModHub\Modules\Tag\Transaction;

use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Transaction\Transaction;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class TagTransaction extends Transaction
{
    const TYPE_EDIT_LABEL = "edit.label";
    const TYPE_EDIT_DESC  = "edit.description";
    const TYPE_EDIT_COLOR = "edit.color";
    const TYPE_EDIT_ORDER = "edit.displayorder";

    public function getEntityClass()
    {
        return 'AnhNhan\ModHub\Modules\Tag\Storage\Tag';
    }

    public function applyTransaction($entity, $xactType, $value)
    {
        //...
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
