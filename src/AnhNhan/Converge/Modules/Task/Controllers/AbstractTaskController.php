<?php
namespace AnhNhan\Converge\Modules\Task\Controllers;

use AnhNhan\Converge\Modules\Task\Query\TaskQuery;
use AnhNhan\Converge\Modules\Task\Storage\Task;
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

    protected function retrieveTaskObject($request, $query)
    {
        if ($roleId = $request->request->get('id'))
        {
            $role = $query->retrieveTasksForUids(["TASK-" . $roleId]);
            if (!$role)
            {
                $role = $query->retrieveTasksForCanonicalLabels([$roleId]);
            }

            $role = idx($role, 0);
        } else
        {
            $role = new Task;
        }

        return $role;
    }
}
