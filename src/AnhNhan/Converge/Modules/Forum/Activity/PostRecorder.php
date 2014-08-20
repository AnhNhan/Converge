<?php
namespace AnhNhan\Converge\Modules\Forum\Activity;

use AnhNhan\Converge\Modules\Activity\ActivityRecorder;
use AnhNhan\Converge\Modules\Forum\Storage\PostTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class PostRecorder extends ActivityRecorder
{
    public function getRecordedTransactionTypes()
    {
        return [
            TransactionEntity::TYPE_CREATE => true,
            PostTransaction::TYPE_EDIT_POST => true,
        ];
    }

    protected function get_object_label(TransactionEntity $xact)
    {
        return sprintf('Post in %s', $xact->object->parentDisq->label);
    }

    protected function get_object_link(TransactionEntity $xact)
    {
        return urisprintf('disq/%s#%s', $xact->object->parentDisq->cleanId, $xact->object->uid);
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
        if ($xact->type == PostTransaction::TYPE_EDIT_POST)
        {
            return $xact->object->createdAt->getTimestamp() == $xact->object->modifiedAt->getTimestamp();
        }

        return parent::dont_record_xact($xact);
    }
}
