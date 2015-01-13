<?php
namespace AnhNhan\Converge\Modules\Subscription\Controllers;

use AnhNhan\Converge\Modules\Subscription\Query\SubscriptionQuery;
use AnhNhan\Converge\Web\Application\BaseApplicationController;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class SubscriptionController extends BaseApplicationController
{
    protected function buildQuery()
    {
        $query = new SubscriptionQuery($this->app);
        return $query;
    }
}
