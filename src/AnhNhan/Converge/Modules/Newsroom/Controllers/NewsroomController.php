<?php
namespace AnhNhan\Converge\Modules\Newsroom\Controllers;

use AnhNhan\Converge\Web\Application\BaseApplicationController;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class NewsroomController extends BaseApplicationController
{
    final protected function buildArticleQuery()
    {
        $query = new ArticleQuery($this->app);
        return $query;
    }
}
