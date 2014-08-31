<?php
namespace AnhNhan\Converge\Modules\Newsroom\Activity;

use AnhNhan\Converge\Modules\Activity\ActivityRecorder;
use AnhNhan\Converge\Modules\Newsroom\Storage\ChannelTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ChannelRecorder extends ActivityRecorder
{
    public function getRecordedTransactionTypes()
    {
        return [
            TransactionEntity::TYPE_CREATE => true,
            ChannelTransaction::TYPE_EDIT_LABEL => true,
        ];
    }

    protected function get_object_label(TransactionEntity $xact)
    {
        return $xact->object->label;
    }

    protected function get_object_link(TransactionEntity $xact)
    {
        return urisprintf('newsroom/#%s', $xact->object->uid);
    }

    protected function get_xact_contents(TransactionEntity $xact)
    {
        if ($xact->type == ChannelTransaction::TYPE_EDIT_LABEL)
        {
            return $xact->oldValue;
        }

        return $xact->newValue;
    }

    protected function dont_record_xact(TransactionEntity $xact)
    {
        if ($xact->type != TransactionEntity::TYPE_CREATE)
        {
            return $xact->object->createdAt->getTimestamp() == $xact->object->modifiedAt->getTimestamp();
        }

        return parent::dont_record_xact($xact);
    }
}
