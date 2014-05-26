<?php
namespace AnhNhan\ModHub\Modules\User\Query;

use AnhNhan\ModHub\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class UserQuery extends Query
{
    const ENTITY_USER = 'AnhNhan\ModHub\Modules\User\Storage\User';

    /**
     * @return \AnhNhan\ModHub\Modules\User\Storage\User
     */
    public function retrieveUser($id)
    {
        return $this
            ->repository(self::ENTITY_USER)
            ->find($id)
        ;
    }

    public function retrieveUsersForIDs(array $ids, $limit = null, $offset = null)
    {
        $userRepo = $this->repository(self::ENTITY_USER);

        $users = $userRepo->findBy(array("id" => $ids), array(), $limit, $offset);
        return $users;
    }

    public function retrieveUsers($limit = null, $offset = null)
    {
        $userRepo = $this->repository(self::ENTITY_USER);

        $users = $userRepo->findBy(array(), array(), $limit = null, $offset = null);
        return $users;
    }

    public function retrieveUsersForNames(array $names, $limit = null, $offset = null)
    {
        $userRepo = $this->repository(self::ENTITY_USER);

        $users = $userRepo->findBy(array("username" => $names), array(), $limit, $offset);
        return $users;
    }
}
