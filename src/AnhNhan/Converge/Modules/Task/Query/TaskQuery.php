<?php
namespace AnhNhan\Converge\Modules\Task\Query;

use AnhNhan\Converge\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskQuery extends Query
{
    const TASK_ENTITY = 'AnhNhan\Converge\Modules\Task\Storage\Task';
    const TASK_STATUS_ENTITY = 'AnhNhan\Converge\Modules\Task\Storage\TaskStatus';
    const TASK_PRIORITY_ENTITY = 'AnhNhan\Converge\Modules\Task\Storage\TaskPriority';

    public function retrieveTasks($limit = null, $offset = null)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        return $query->getResult();
    }

    public function retrieveIncompleteTasks($limit = null, $offset = null)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta WHERE t.completed = 0";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        return $query->getResult();
    }

    public function retrieveTasksForUids(array $ids)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta WHERE t.uid IN (:task_ids)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array("task_ids" => $ids))
        ;
        return $query->getResult();
    }

    public function retrieveTasksForLabels(array $labels)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta WHERE t.label IN (:task_labels)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array("task_labels" => $labels))
        ;
        return $query->getResult();
    }

    public function retrieveTasksForCanonicalLabels(array $labels)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta WHERE t.label_canonical IN (:task_labels)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array("task_labels" => $labels))
        ;
        return $query->getResult();
    }

    public function retrieveTasksForCanonicalLabelsWithXacts(array $labels)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta, tx FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta LEFT JOIN t.xacts tx WHERE t.label_canonical IN (:task_labels)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array("task_labels" => $labels))
        ;
        return $query->getResult();
    }

    public function retrieveTaskStatus($limit = null, $offset = null)
    {
        $eTask = self::TASK_STATUS_ENTITY;
        $queryString = "SELECT ts FROM {$eTask} ts";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        return mkey($query->getResult(), 'uid');
    }

    public function retrieveTaskStatusForUids(array $ids)
    {
        $eTask = self::TASK_STATUS_ENTITY;
        $queryString = "SELECT ts FROM {$eTask} ts WHERE ts.uid IN (:task_ids)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array("task_ids" => $ids))
        ;
        return mkey($query->getResult(), 'uid');
    }

    public function retrieveTaskStatusForLabels(array $labels)
    {
        $eTask = self::TASK_STATUS_ENTITY;
        $queryString = "SELECT ts FROM {$eTask} ts WHERE ts.label IN (:task_labels)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array("task_labels" => $labels))
        ;
        return mkey($query->getResult(), 'uid');
    }

    public function retrieveTaskPriorities($limit = null, $offset = null)
    {
        $eTask = self::TASK_PRIORITY_ENTITY;
        $queryString = "SELECT tp FROM {$eTask} tp";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        return mkey($query->getResult(), 'uid');
    }

    public function retrieveTaskPriorityForUids(array $ids)
    {
        $eTask = self::TASK_PRIORITY_ENTITY;
        $queryString = "SELECT tp FROM {$eTask} tp WHERE tp.uid IN (:task_ids)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array("task_ids" => $ids))
        ;
        return mkey($query->getResult(), 'uid');
    }

    public function retrieveTaskPriorityForLabels(array $labels)
    {
        $eTask = self::TASK_PRIORITY_ENTITY;
        $queryString = "SELECT tp FROM {$eTask} tp WHERE tp.label IN (:task_labels)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array("task_labels" => $labels))
        ;
        return mkey($query->getResult(), 'uid');
    }
}
