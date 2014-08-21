<?php
namespace AnhNhan\Converge\Modules\Activity\Query;

use AnhNhan\Converge\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ActivityQuery extends Query
{
    const ENTITY_ACTIVITY = 'AnhNhan\Converge\Modules\Activity\Storage\RecordedActivity';

    public function retrieveActivities($limit = null, $offset = null)
    {
        return mkey($this
            ->repository(self::ENTITY_ACTIVITY)
            ->findBy([], [], $limit, $offset), 'uid');
    }

    public function retrieveActivitiesByUsers(array $user_uids, $limit = null, $offset = null)
    {
        return mkey($this
            ->repository(self::ENTITY_ACTIVITY)
            ->findBy(['actor_uid' => $user_uids], [], $limit, $offset), 'uid');
    }

    public function retrieveActivitiesByObjects(array $object_uids, $limit = null, $offset = null)
    {
        return mkey($this
            ->repository(self::ENTITY_ACTIVITY)
            ->findBy(['object_uid' => $object_uids], [], $limit, $offset), 'uid');
    }
}
