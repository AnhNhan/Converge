<?php
namespace AnhNhan\ModHub\Modules\Forum\Query;

use AnhNhan\ModHub\Storage\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionQuery extends Query
{
    const ENTITY_DISCUSSION = "AnhNhan\\ModHub\\Modules\\Forum\\Storage\\Discussion";
    const ENTITY_DISCUSSION_XACT = "AnhNhan\\ModHub\\Modules\\Forum\\Storage\\DiscussionTransaction";
    const ENTITY_POST = "AnhNhan\\ModHub\\Modules\\Forum\\Storage\\Post";
    const ENTITY_POST_XACT = "AnhNhan\\ModHub\\Modules\\Forum\\Storage\\PostTransaction";

    /**
     * @return \AnhNhan\ModHub\Modules\Forum\Storage\Discussion
     */
    public function retrieveDiscussion($id)
    {
        return $this
            ->repository(self::ENTITY_DISCUSSION)
            ->find($id)
        ;
    }

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

    public function getPaginatorForDiscussionTransactions($disqId, $limit, $offset)
    {
        $queryString = sprintf(
            "SELECT xact FROM %s xact WHERE xact.object = :disq_id ORDER BY xact.createdAt ASC",
            self::ENTITY_DISCUSSION_XACT
        );
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameters(array("disq_id" => $disqId))
        ;

        $paginator = new Paginator($query, false);
        return $paginator;
    }

    /**
     * @return \AnhNhan\ModHub\Modules\Forum\Storage\Post
     */
    public function retrievePost($id)
    {
        return $this
            ->repository(self::ENTITY_POST)
            ->find($id)
        ;
    }

    public function retrievePostsForIDs(array $ids, $limit = null, $offset = null)
    {
        return $this
            ->repository(self::ENTITY_POST)
            ->findBy(array("id" => $ids), array("createdAt" => "ASC"), $limit, $offset)
        ;
    }

    public function retrievePosts($limit = null, $offset = null)
    {
        return $this
            ->repository(self::ENTITY_POST)
            ->findBy(array(), array("createdAt" => "ASC"), $limit, $offset)
        ;
    }

}
