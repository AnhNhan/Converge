<?php
namespace AnhNhan\Converge\Modules\Task\Controllers;

use AnhNhan\Converge\Modules\Task\Query\TaskQuery;
use AnhNhan\Converge\Web\Application\BaseApplicationController;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class AbstractTaskController extends BaseApplicationController
{
    protected function buildQuery()
    {
        $query = new TaskQuery($this->app);
        return $query;
    }
}
