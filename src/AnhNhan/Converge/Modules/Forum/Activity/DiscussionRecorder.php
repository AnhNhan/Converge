<?php
namespace AnhNhan\Converge\Modules\Forum\Activity;

use AnhNhan\Converge\Modules\Activity\ActivityRecorder;
use AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionRecorder extends ActivityRecorder
{
    public function getRecordedTransactionTypes()
    {
        return [
            TransactionEntity::TYPE_CREATE => true,
            DiscussionTransaction::TYPE_EDIT_TEXT => true,
        ];
    }

    protected function get_object_label(TransactionEntity $xact)
    {
        return $xact->object->label;
    }

    protected function get_object_link(TransactionEntity $xact)
    {
        return urisprintf('disq/%s', $xact->object->cleanId);
    }

    protected function get_xact_contents(TransactionEntity $xact)
    {
        if ($xact->type == TransactionEntity::TYPE_CREATE)
        {
            return $xact->object->rawText;
        }

        return $xact->newValue;
    }

    protected function dont_record_xact(TransactionEntity $xact)
    {
        if ($xact->type == DiscussionTransaction::TYPE_EDIT_TEXT)
        {
            return $xact->object->createdAt->getTimestamp() == $xact->object->lastActivity->getTimestamp();
        }

        return parent::dont_record_xact($xact);
    }
}
