<?php
namespace AnhNhan\Converge\Modules\Draft\Controllers;

use AnhNhan\Converge\Modules\Draft\DraftQuery;
use AnhNhan\Converge\Web\Application\BaseApplicationController;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class DraftController extends BaseApplicationController
{
    protected function buildQuery()
    {
        $query = new DraftQuery($this->app);
        return $query;
    }
}
