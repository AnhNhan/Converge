<?php
namespace AnhNhan\Converge\Modules\Newsroom\Controllers;

use AnhNhan\Converge\Modules\Newsroom\Query\ArticleQuery;
use AnhNhan\Converge\Web\Application\BaseApplicationController;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class NewsroomController extends BaseApplicationController
{
    final protected function buildArticleQuery()
    {
        $query = new ArticleQuery($this->app);
        $query->addExternalQueryFromApplication(ArticleQuery::EXT_QUERY_TAG, $this->externalApp('tag'));
        $query->addExternalQueryFromApplication(ArticleQuery::EXT_QUERY_USER, $this->externalApp('people'));
        return $query;
    }
}
