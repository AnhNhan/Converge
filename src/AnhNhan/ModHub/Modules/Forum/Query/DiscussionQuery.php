<?php
namespace AnhNhan\ModHub\Modules\Forum\Query;

use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Storage\Query;
use Doctrine\ORM\Query as DoctrineQuery;
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
        $eDisq = self::ENTITY_DISCUSSION;
        $queryString = "SELECT d, dt FROM {$eDisq} d JOIN d.tags dt WHERE d.id = :disq_id";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array("disq_id" => $id))
        ;
        return idx($query->getResult(), 0);
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
        $eDisq = self::ENTITY_DISCUSSION;
        $queryString = "SELECT d, dt FROM {$eDisq} d JOIN d.tags dt";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        $paginator = new Paginator($query, true);
        return $paginator->getIterator()->getArrayCopy();
    }

    public function retriveDiscussionsForTag(Tag $tag, $limit = null, $offset = null)
    {
        $eDisq = self::ENTITY_DISCUSSION;
        $tagId = $tag->uid();
        $queryString = "SELECT d FROM {$eDisq} d JOIN d.tags t WHERE t.t_id = :tag_id";
        $query = $this->em()->createQuery($queryString);
        $query->setParameters(array('tag_id' => $tagId));
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

    public function fetchPostCountsForDiscussions(array $disqs)
    {
        assert_instances_of($disqs, self::ENTITY_DISCUSSION);

        $ePost = self::ENTITY_POST;
        $eDisq = self::ENTITY_DISCUSSION;
        $queryString = "SELECT d.id, COUNT(p.id) AS postcount
            FROM {$eDisq} d INDEX BY d.id
                JOIN d.posts p
            WHERE d.id IN (:disq_ids)
                AND p.deleted = 0
            GROUP BY d.id";
        $query = $this->em()->createQuery($queryString);
        $query->setParameters(array('disq_ids' => mpull($disqs, 'uid')));
        return $query->getResult(DoctrineQuery::HYDRATE_ARRAY);
    }

    public function fetchExternalsForDiscussions(array $disqs)
    {
        assert_instances_of($disqs, self::ENTITY_DISCUSSION);
        $authors = mpull($disqs, 'author');
        if (count(array_filter($authors)) != count($authors)) { // Can this check be optimized?
            $this->requireExternalQuery(self::EXT_QUERY_USER);
            // TODO: Finish this once we really have users
        }

        $disq_tags = mpull(mpull($disqs, 'tags'), 'toArray');
        $tags_flat = array_mergev($disq_tags);

        try {
            // Test if >=1 DiscussionTags don't have a tag set by accessing it
            mpull($tags_flat, 'tag');
        } catch (\Exception $e) {
            // Apparently we have >=1 tags not loaded - batch load them
            $tag_ids = mpull($tags_flat, 'tagId');
            $tagQuery = $this->requireExternalQuery(self::EXT_QUERY_TAG);
            $tag_objs = $tagQuery->retrieveTagsForIDs($tag_ids);
            $tag_objs = mpull($tag_objs, null, 'uid');

            foreach ($tags_flat as $tag) {
                $tag->setTag($tag_objs[$tag->tagId]);
            }
        }
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
