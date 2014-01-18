<?php
namespace AnhNhan\ModHub\Modules\User\Query;

use AnhNhan\ModHub\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class RoleQuery extends Query
{
    const ENTITY_ROLE = 'AnhNhan\ModHub\Modules\User\Storage\Role';

    /**
     * @return \AnhNhan\ModHub\Modules\User\Storage\Role
     */
    public function retrieveRole($id)
    {
        return $this
            ->repository(self::ENTITY_ROLE)
            ->find($id)
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
