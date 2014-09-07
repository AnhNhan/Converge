<?php
namespace AnhNhan\Converge\Modules\Draft;

use AnhNhan\Converge\Storage\Query;
use Doctrine\ORM\Query as DoctrineQuery;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DraftQuery extends Query
{
    const ENTITY_DRAFT = "AnhNhan\\Converge\\Modules\\Draft\\Storage\\DraftObject";

    public function retrieveDraftsForUsers(array $ids, $limit = null, $offset = null)
    {
        return $this
            ->repository(self::ENTITY_DRAFT)
            ->findBy(["user_uid" => $ids], [], $limit, $offset)
        ;
    }

    public function retrieveDraftObject($user_uid, $object_uid)
    {
        return head($this
            ->repository(self::ENTITY_DRAFT)
            ->findBy(["user_uid" => $user_uid, "object_uid" => $object_uid])
        );
    }
}
