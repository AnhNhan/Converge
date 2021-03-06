<?php
namespace AnhNhan\Converge\Modules\Task\Activity;

use AnhNhan\Converge\Modules\Activity\ActivityRecorder;
use AnhNhan\Converge\Modules\Task\Storage\TaskTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskRecorder extends ActivityRecorder
{
    public function getRecordedTransactionTypes()
    {
        return [
            TransactionEntity::TYPE_CREATE => true,
            TaskTransaction::TYPE_EDIT_LABEL => true,
            TaskTransaction::TYPE_EDIT_DESC => true,
            TaskTransaction::TYPE_EDIT_CLOSED => true,
            TaskTransaction::TYPE_ADD_COMMENT => true,
            TaskTransaction::TYPE_ADD_ASSIGN => true,
            TaskTransaction::TYPE_DEL_ASSIGN => true,
        ];
    }

    protected function get_object_label(TransactionEntity $xact)
    {
        if ($xact->type == TaskTransaction::TYPE_EDIT_LABEL)
        {
            return $xact->oldValue;
        }

        return $xact->object->label;
    }

    protected function get_object_link(TransactionEntity $xact)
    {
        return urisprintf('task/%s', $xact->object->label_canonical);
    }

    protected function get_xact_contents(TransactionEntity $xact)
    {
        if ($xact->type == TransactionEntity::TYPE_CREATE)
        {
            return $xact->object->description;
        }

        if ($xact->type == TaskTransaction::TYPE_DEL_ASSIGN)
        {
            return $xact->oldValue;
        }

        return $xact->newValue;
    }

    protected function dont_record_xact(TransactionEntity $xact)
    {
        if ($xact->type == TaskTransaction::TYPE_EDIT_LABEL || $xact->type == TaskTransaction::TYPE_EDIT_DESC)
        {
            return $xact->object->createdAt->getTimestamp() == $xact->object->modifiedAt->getTimestamp();
        }

        return parent::dont_record_xact($xact);
    }
}
