<?php
namespace AnhNhan\Converge\Modules\Forum\Query;

use AnhNhan\Converge\Modules\Forum\Storage\Discussion;
use AnhNhan\Converge\Storage\Query;
use Doctrine\ORM\Query as DoctrineQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionQuery extends Query
{
    const ENTITY_DISCUSSION = 'AnhNhan\\Converge\\Modules\\Forum\\Storage\\Discussion';
    const ENTITY_DISCUSSION_XACT = 'AnhNhan\\Converge\\Modules\\Forum\\Storage\\DiscussionTransaction';
    const ENTITY_DISCUSSION_TAG = 'AnhNhan\\Converge\\Modules\\Forum\\Storage\\DiscussionTag';
    const ENTITY_POST = 'AnhNhan\\Converge\\Modules\\Forum\\Storage\\Post';
    const ENTITY_POST_XACT = 'AnhNhan\\Converge\\Modules\\Forum\\Storage\\PostTransaction';

    /**
     * @return \AnhNhan\Converge\Modules\Forum\Storage\Discussion
     */
    public function retrieveDiscussion($id)
    {
        $eDisq = self::ENTITY_DISCUSSION;
        $queryString = "SELECT d, dt, dc FROM {$eDisq} d LEFT JOIN d.tags dt LEFT JOIN d.comments dc WHERE d.uid = :disq_id";
        $query = $this->em()
            ->createQuery($queryString)
            ->setParameters(array('disq_id' => $id))
        ;
        return idx($query->getResult(), 0);
    }

    public function retrieveDiscussionForIDs(array $ids, $limit = null, $offset = null)
    {
        return $this
            ->repository(self::ENTITY_DISCUSSION)
            ->findBy(array('uid' => $ids), array('lastActivity' => 'DESC'), $limit, $offset)
        ;
    }

    public function retrieveDiscussionForAuthorUIDs(array $author_ids, $limit = null, $offset = null)
    {
        $eDisq = self::ENTITY_DISCUSSION;
        $queryString = "SELECT d, dt FROM {$eDisq} d JOIN d.tags dt WHERE d.author IN (:author_ids) ORDER BY d.lastActivity DESC";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameters(['author_ids' => $author_ids])
        ;

        return $query->getResult();
    }

    public function retrieveDiscussions($limit = null, $offset = null)
    {
        $eDisq = self::ENTITY_DISCUSSION;
        $queryString = "SELECT d, dt FROM {$eDisq} d LEFT JOIN d.tags dt ORDER BY d.lastActivity DESC";
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
        $queryString = "SELECT d FROM {$eDisq} d JOIN d.tags t WHERE t.t_id = :tag_id ORDER BY d.lastActivity DESC";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        $query->setParameters(array('tag_id' => $tagId));
        return $query->getResult();
    }

    public function retrieveDiscussionsSearchTags(array $tags_inc, array $tags_exc, $limit = null, $offset = null)
    {
        if ($tags_exc)
        {
            throw new \Exception('Tag exclusions are not supported yet.');
        }

        // Using two separate queries because Doctrine would not allow us to
        // select the disq_id field for subquery result.
        $eDisq = self::ENTITY_DISCUSSION;
        $queryString = "
            SELECT d, dt
            FROM {$eDisq} d
                JOIN d.tags dt
            WHERE d.id IN (:disq_inc_ids)
            ORDER BY d.lastActivity DESC
        ";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        $query->setParameters([
            'disq_inc_ids' => ipull($this->aoefs_subQuery($tags_inc), 'disq_id'),
            //'disq_exc_ids' => ipull($this->aoefs_subQuery($tags_exc), 'disq_id'),
        ]);
        return $query->getResult();
    }

    private function aoefs_subQuery(array $ids)
    {
        $eDisqTag = self::ENTITY_DISCUSSION_TAG;
        $subQueryString = "
            SELECT dt
            FROM {$eDisqTag} dt
            WHERE dt.t_id IN (:disq_inc_ids)
            GROUP BY dt.disq
            HAVING COUNT(dt.t_id) >= :disq_inc_count
        ";
        $subQuery = $this->em()
            ->createQuery($subQueryString)
        ;
        $subQuery->setParameters(['disq_inc_ids' => $ids, 'disq_inc_count' => count($ids)]);
        return $subQuery->getResult(DoctrineQuery::HYDRATE_ARRAY);
    }

    public function fetchPostCountsForDiscussions(array $disqs)
    {
        assert_instances_of($disqs, self::ENTITY_DISCUSSION);

        $ePost = self::ENTITY_POST;
        $eDisq = self::ENTITY_DISCUSSION;
        $queryString = "SELECT d.uid, COUNT(p.id) AS postcount
            FROM {$eDisq} d INDEX BY d.uid
                JOIN d.posts p
            WHERE d.uid IN (:disq_ids)
                AND p.deleted = 0
            GROUP BY d.id";
        $query = $this->em()
            ->createQuery($queryString)
        ;
        $query->setParameters(array('disq_ids' => mpull($disqs, 'uid')));
        return $query->getResult(DoctrineQuery::HYDRATE_ARRAY);
    }

    public function fetchExternalsForDiscussions(array $disqs)
    {
        assert_instances_of($disqs, self::ENTITY_DISCUSSION);

        fetch_external_authors($disqs, $this->requireExternalQuery(self::EXT_QUERY_USER));

        $disq_tags = mpull(mpull($disqs, 'tags'), 'toArray');
        $tags_flat = array_mergev($disq_tags);
        $tagQuery = $this->requireExternalQuery(self::EXT_QUERY_TAG);
        fetch_external_tags($tags_flat, $tagQuery);
    }

    /**
     * @return \AnhNhan\Converge\Modules\Forum\Storage\Post
     */
    public function retrievePost($id)
    {
        return idx($this
            ->repository(self::ENTITY_POST)
            ->findBy(array('uid' => $id), array(), 1)
        , 0);
    }

    public function retrievePostsForIDs(array $ids, $limit = null, $offset = null)
    {
        return $this
            ->repository(self::ENTITY_POST)
            ->findBy(array('uid' => $ids), array('createdAt' => 'ASC'), $limit, $offset)
        ;
    }

    public function retrievePosts($limit = null, $offset = null)
    {
        return $this
            ->repository(self::ENTITY_POST)
            ->findBy(array(), array('createdAt' => 'ASC'), $limit, $offset)
        ;
    }

    public function retrivePostsForDiscussion(Discussion $disq, $limit = null, $offset = null)
    {
        $ePost = self::ENTITY_POST;
        $queryString = "SELECT p, pc FROM {$ePost} p LEFT JOIN p.comments pc WHERE p.disq = :disq ORDER BY p.createdAt ASC";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        $query->setParameters(array('disq' => $disq));
        return $query->getResult();
    }

}
