<?php
namespace AnhNhan\Converge\Modules\Task\Query;

use AnhNhan\Converge\Modules\Task\Storage\Task;
use AnhNhan\Converge\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskQuery extends Query
{
    const TASK_ENTITY = 'AnhNhan\Converge\Modules\Task\Storage\Task';
    const TASK_STATUS_ENTITY = 'AnhNhan\Converge\Modules\Task\Storage\TaskStatus';
    const TASK_PRIORITY_ENTITY = 'AnhNhan\Converge\Modules\Task\Storage\TaskPriority';
    const TASK_RELATION_ENTITY = 'AnhNhan\Converge\Modules\Task\Storage\TaskRelation';
    const TASK_SUBTASK_ENTITY = 'AnhNhan\Converge\Modules\Task\Storage\TaskSubTask';
    const TASK_BLOCKER_ENTITY = 'AnhNhan\Converge\Modules\Task\Storage\TaskBlocker';

    public function retrieveTasks($limit = null, $offset = null)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta, tt FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta LEFT JOIN t.tags tt";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        return $query->getResult();
    }

    public function retrieveUnclosedTasks($limit = null, $offset = null)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta, tt FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta LEFT JOIN t.tags tt WHERE t.closed = 0";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        return $query->getResult();
    }

    public function retrieveTasksForAssigned(array $assigned_ids, $closed = null, $limit = null, $offset = null)
    {
        $closed_sql = $closed === null ? '' : ' AND t.closed = ' . ($closed ? '1' : '0');
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta, tt FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta LEFT JOIN t.tags tt WHERE ta.user IN (:assigned_ids){$closed_sql}";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameters(['assigned_ids' => $assigned_ids])
        ;
        return $query->getResult();
    }

    public function retrieveTasksForUids(array $ids)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta, tt FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta LEFT JOIN t.tags tt WHERE t.uid IN (:task_ids)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array('task_ids' => $ids))
        ;
        return $query->getResult();
    }

    public function retrieveTasksForLabels(array $labels)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta, tt FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta LEFT JOIN t.tags tt WHERE t.label IN (:task_labels)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array('task_labels' => $labels))
        ;
        return $query->getResult();
    }

    public function retrieveTasksForCanonicalLabels(array $labels)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta, tt FROM {$eTask} t JOIN t.status ts JOIN t.priority tp LEFT JOIN t.assigned ta LEFT JOIN t.tags tt WHERE t.label_canonical IN (:task_labels)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array('task_labels' => $labels))
        ;
        return $query->getResult();
    }

    public function retrieveTasksForCanonicalLabelsWithXacts(array $labels)
    {
        $eTask = self::TASK_ENTITY;
        $queryString = "SELECT t, ts, tp, ta, tt, tx, t_blocking, t_blocked, t_sub, t_parent
        FROM {$eTask} t
            JOIN t.status ts
            JOIN t.priority tp
            LEFT JOIN t.assigned ta
            LEFT JOIN t.tags tt
            LEFT JOIN t.xacts tx
            LEFT JOIN t.blockedTasks t_blocking
            LEFT JOIN t.blockedBy t_blocked
            LEFT JOIN t.subTasks t_sub
            LEFT JOIN t.parentTasks t_parent
        WHERE t.label_canonical IN (:task_labels)
        ";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array('task_labels' => $labels))
        ;
        return $query->getResult();
    }

    public function fetchExternalTags(array $tasks)
    {
        assert_instances_of($tasks, self::TASK_ENTITY);

        $task_tags = mpull(array_filter(mpull($tasks, 'tags')), 'toArray');
        $tags_flat = array_mergev($task_tags);
        $tag_query = $this->requireExternalQuery(self::EXT_QUERY_TAG);
        fetch_external_tags($tags_flat, $tag_query);
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
            ->setParameters(array('task_ids' => $ids))
        ;
        return mkey($query->getResult(), 'uid');
    }

    public function retrieveTaskStatusForLabels(array $labels)
    {
        $eTask = self::TASK_STATUS_ENTITY;
        $queryString = "SELECT ts FROM {$eTask} ts WHERE ts.label IN (:task_labels)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array('task_labels' => $labels))
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
            ->setParameters(array('task_ids' => $ids))
        ;
        return mkey($query->getResult(), 'uid');
    }

    public function retrieveTaskPriorityForLabels(array $labels)
    {
        $eTask = self::TASK_PRIORITY_ENTITY;
        $queryString = "SELECT tp FROM {$eTask} tp WHERE tp.label IN (:task_labels)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array('task_labels' => $labels))
        ;
        return mkey($query->getResult(), 'uid');
    }

    public function searchBlocker(Task $parent, Task $child, $limit = null, $offset = null)
    {
        return $this
            ->repository(self::TASK_BLOCKER_ENTITY)
            ->findBy(['parentTask' => $parent, 'blockingTask' => $child], [], $limit, $offset)
        ;
    }

    public function searchSubTask(Task $parent, Task $child, $limit = null, $offset = null)
    {
        return $this
            ->repository(self::TASK_SUBTASK_ENTITY)
            ->findBy(['parentTask' => $parent, 'subTask' => $child], [], $limit, $offset)
        ;
    }
}
