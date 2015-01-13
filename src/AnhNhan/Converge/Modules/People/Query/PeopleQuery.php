<?php
namespace AnhNhan\Converge\Modules\People\Query;

use AnhNhan\Converge\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class PeopleQuery extends Query
{
    const ENTITY_USER = 'AnhNhan\Converge\Modules\People\Storage\User';
    const ENTITY_EMAIL = 'AnhNhan\Converge\Modules\People\Storage\Email';

    /**
     * @return \AnhNhan\Converge\Modules\People\Storage\User
     */
    public function retrieveUser($id)
    {
        return idx($this->repository(self::ENTITY_USER)
            ->findBy(array("uid" => $id), array(), 1), 0);
    }

    public function retrieveUsersForUIDs(array $ids, $limit = null, $offset = null)
    {
        return mkey($this->repository(self::ENTITY_USER)
            ->findBy(array("uid" => $ids), array(), $limit, $offset), 'uid');
    }

    public function retrieveUsers($limit = null, $offset = null)
    {
        $userRepo = $this->repository(self::ENTITY_USER);

        $users = mkey($userRepo->findBy(array(), array(), $limit, $offset), 'uid');
        return $users;
    }

    public function retrieveUsersForNames(array $names, $limit = null, $offset = null)
    {
        $userRepo = $this->repository(self::ENTITY_USER);

        $users = mkey($userRepo->findBy(array("username" => $names), array(), $limit, $offset), 'uid');
        return $users;
    }

    public function retrieveUsersForCanonicalNames(array $names, $limit = null, $offset = null)
    {
        $userRepo = $this->repository(self::ENTITY_USER);

        $users = mkey($userRepo->findBy(array("name_canon" => $names), array(), $limit, $offset), 'uid');
        return $users;
    }

    // *************************************************************************
    //                                EMAIL
    // *************************************************************************

    public function retrieveEmailsForUIDs(array $ids, $limit = null, $offset = null)
    {
        return mkey($this->repository(self::ENTITY_EMAIL)
            ->findBy(array("uid" => $ids), array("email" => "ASC"), $limit, $offset), 'uid');
    }

    public function retrieveEmailsForNames(array $names, $limit = null, $offset = null)
    {
        return mkey($this->repository(self::ENTITY_EMAIL)
            ->findBy(array("email" => $names), array("email" => "ASC"), $limit, $offset), 'uid');
    }
}
