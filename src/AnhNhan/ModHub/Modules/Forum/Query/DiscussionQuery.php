<?php
namespace AnhNhan\ModHub\Modules\Forum\Query;

use AnhNhan\ModHub\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionQuery extends Query
{
    const ENTITY_DISCUSSION = "AnhNhan\\ModHub\\Modules\\Forum\\Storage\\Discussion";
    const ENTITY_POST = "AnhNhan\\ModHub\\Modules\\Forum\\Storage\\Post";

    public function retrieveDiscussionForIDs(array $ids, $limit = null, $offset = null)
    {
        return $this
            ->repository(self::ENTITY_DISCUSSION)
            ->findBy(array("id" => $ids), array("lastActivity" => "DESC"), $limit, $offset)
        ;
    }

    public function retrieveDiscussions($limit = null, $offset = null)
    {
        return $this
            ->repository(self::ENTITY_DISCUSSION)
            ->findBy(array(), array("lastActivity" => "DESC"), $limit, $offset)
        ;
    }

    public function retriveDiscussionsForTag(Tag $tag, $limit = null, $offset = null)
    {
        $eDisq = self::ENTITY_DISCUSSION;
        $tagId = $tag->uid();
        $queryString = "SELECT d FROM {$eDisq} d JOIN d.tags t WHERE t.t_id = '{$tagId}'";
        $query = $this->em()->createQuery($queryString);
        return $query->getResult();
    }
}
