<?php
namespace AnhNhan\Converge\Modules\Newsroom\Storage;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class ArticleTransaction extends TransactionEntity
{
    const TYPE_EDIT_TITLE = 'article.edit.title';
    const TYPE_EDIT_CHANNEL = 'article.edit.channel';
    const TYPE_EDIT_BYLINE = 'article.edit.byline';
    const TYPE_ADD_AUTHOR = 'article.add.author';
    const TYPE_DEL_AUTHOR = 'article.del.author';
}
