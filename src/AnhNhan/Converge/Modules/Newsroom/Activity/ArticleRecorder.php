<?php
namespace AnhNhan\Converge\Modules\Newsroom\Activity;

use AnhNhan\Converge\Modules\Activity\ActivityRecorder;
use AnhNhan\Converge\Modules\Newsroom\Storage\ArticleTransaction;
use AnhNhan\Converge\Modules\Newsroom\Storage\DMArticleTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * Combined recorder for all article types
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ArticleRecorder extends ActivityRecorder
{
    public function getRecordedTransactionTypes()
    {
        return [
            TransactionEntity::TYPE_CREATE => true,
            ArticleTransaction::TYPE_EDIT_TITLE => true,
            DMArticleTransaction::TYPE_EDIT_TEXT => true,
        ];
    }

    protected function get_object_label(TransactionEntity $xact)
    {
        return \AnhNhan\Converge\hsprintf('<strong>%s</strong> <span class="muted">in</span> %s', phutil_utf8_shorten($xact->object->title, 40), $xact->object->channel->label);
    }

    protected function get_object_link(TransactionEntity $xact)
    {
        return urisprintf('a/%p/%p', $xact->object->channel->slug, $xact->object->slug);
    }

    protected function get_xact_contents(TransactionEntity $xact)
    {
        if ($xact->type == TransactionEntity::TYPE_CREATE)
        {
            return $xact->object->rawText;
        }

        if ($xact->type == ArticleTransaction::TYPE_EDIT_TITLE)
        {
            return phutil_utf8_shorten($xact->oldValue, 40);
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
