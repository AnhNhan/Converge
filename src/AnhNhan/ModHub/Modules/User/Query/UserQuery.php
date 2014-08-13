<?php
namespace AnhNhan\ModHub\Modules\User\Query;

use AnhNhan\ModHub\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class UserQuery extends Query
{
    const ENTITY_USER = 'AnhNhan\ModHub\Modules\User\Storage\User';
    const ENTITY_EMAIL = 'AnhNhan\ModHub\Modules\User\Storage\Email';

    /**
     * @return \AnhNhan\ModHub\Modules\User\Storage\User
     */
    public function retrieveUser($id)
    {
        return idx($this->repository(self::ENTITY_USER)
            ->findBy(array("uid" => $id), array(), 1), 0);
    }

    public function retrieveUsersForUIDs(array $ids, $limit = null, $offset = null)
    {
        return $this->repository(self::ENTITY_USER)
            ->findBy(array("uid" => $ids), array(), $limit, $offset);
    }

    public function retrieveUsers($limit = null, $offset = null)
    {
        $userRepo = $this->repository(self::ENTITY_USER);

        $users = $userRepo->findBy(array(), array(), $limit, $offset);
        return $users;
    }

    public function retrieveUsersForNames(array $names, $limit = null, $offset = null)
    {
        $userRepo = $this->repository(self::ENTITY_USER);

        $users = $userRepo->findBy(array("username" => $names), array(), $limit, $offset);
        return $users;
    }

    public function retrieveUsersForCanonicalNames(array $names, $limit = null, $offset = null)
    {
        $userRepo = $this->repository(self::ENTITY_USER);

        $users = $userRepo->findBy(array("name_canon" => $names), array(), $limit, $offset);
        return $users;
    }

    // *************************************************************************
    //                                EMAIL
    // *************************************************************************

    public function retrieveEmailsForUIDs(array $ids, $limit = null, $offset = null)
    {
        return $this->repository(self::ENTITY_EMAIL)
            ->findBy(array("uid" => $ids), array("email" => "ASC"), $limit, $offset);
    }

    public function retrieveEmailsForNames(array $names, $limit = null, $offset = null)
    {
        return $this->repository(self::ENTITY_EMAIL)
            ->findBy(array("email" => $names), array("email" => "ASC"), $limit, $offset);
    }
}
