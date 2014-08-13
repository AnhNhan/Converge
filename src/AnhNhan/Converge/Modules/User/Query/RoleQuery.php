<?php
namespace AnhNhan\Converge\Modules\User\Query;

use AnhNhan\Converge\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class RoleQuery extends Query
{
    const ENTITY_ROLE = 'AnhNhan\Converge\Modules\User\Storage\Role';

    /**
     * @return \AnhNhan\Converge\Modules\User\Storage\Role
     */
    public function retrieveRole($id)
    {
        return idx($this
            ->repository(self::ENTITY_ROLE)
            ->findBy(array("uid" => $id), array("name" => "ASC"), 1), 0)
        ;
    }

    public function retrieveRolesForIDs(array $ids, $limit = null, $offset = null)
    {
        $roleRepo = $this->repository(self::ENTITY_ROLE);

        $roles = $roleRepo->findBy(array("id" => $ids), array("name" => "ASC"), $limit, $offset);
        return $roles;
    }

    public function retrieveRoles($limit = null, $offset = null)
    {
        $roleRepo = $this->repository(self::ENTITY_ROLE);

        $roles = $roleRepo->findBy(array(), array("name" => "ASC"), $limit = null, $offset = null);
        return $roles;
    }

    public function retrieveRolesForNames(array $names, $limit = null, $offset = null)
    {
        $roleRepo = $this->repository(self::ENTITY_ROLE);

        $roles = $roleRepo->findBy(array("name" => $names), array("name" => "ASC"), $limit, $offset);
        return $roles;
    }
}
