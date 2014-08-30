<?php
namespace AnhNhan\Converge\Modules\Newsroom\Controllers;

use AnhNhan\Converge\Modules\Newsroom\Query\ArticleQuery;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class ArticleController extends NewsroomController
{
    final protected function retrieveArticleObject($request, ArticleQuery $query)
    {
        $article = null;
        if ($channel_id = $request->request->get('channel') and $article_id = $request->request->get('id'))
        {
            $article = head($query->searchArticlesInChannel($channel_id, [$article_id]));
        }

        return $article;
    }
}
