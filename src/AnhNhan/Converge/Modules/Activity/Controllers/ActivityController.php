<?php
namespace AnhNhan\Converge\Modules\Activity\Controllers;

use AnhNhan\Converge\Modules\Activity\Query\ActivityQuery;
use AnhNhan\Converge\Modules\Activity\Storage\RecordedActivity;
use AnhNhan\Converge\Web\Application\BaseApplicationController;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class ActivityController extends BaseApplicationController
{
    protected function buildQuery()
    {
        $query = new ActivityQuery($this->app);
        return $query;
    }
}
