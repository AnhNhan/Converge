<?php
namespace AnhNhan\Converge\Modules\Forum\Controllers;

use AnhNhan\Converge\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\Converge\Web\Application\BaseApplicationController;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class AbstractForumController extends BaseApplicationController
{
    protected function buildQuery()
    {
        $query = new DiscussionQuery($this->app);
        $query->addExternalQueryFromApplication(DiscussionQuery::EXT_QUERY_TAG, $this->externalApp('tag'));
        $query->addExternalQueryFromApplication(DiscussionQuery::EXT_QUERY_USER, $this->externalApp('people'));
        return $query;
    }
}
