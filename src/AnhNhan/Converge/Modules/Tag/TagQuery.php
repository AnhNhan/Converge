<?php
namespace AnhNhan\Converge\Modules\Tag;

use AnhNhan\Converge\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TagQuery extends Query
{
    const TAG_ENTITY = 'AnhNhan\Converge\Modules\Tag\Storage\Tag';

    /**
     * @return \AnhNhan\Converge\Modules\Tag\Storage\Tag
     */
    public function retrieveTag($id)
    {
        return $this
            ->repository(self::TAG_ENTITY)
            ->find($id)
        ;
    }

    public function retrieveTagsForIDs(array $ids, $limit = null, $offset = null)
    {
        $tagRepo = $this->repository(self::TAG_ENTITY);

        $tags = $tagRepo->findBy(array("uid" => $ids), array("displayOrder" => "ASC", "label" => "ASC"), $limit, $offset);
        return $tags;
    }

    public function retrieveTags($limit = null, $offset = null)
    {
        $tagRepo = $this->repository(self::TAG_ENTITY);

        $tags = $tagRepo->findBy(array(), array("displayOrder" => "ASC", "label" => "ASC"), $limit = null, $offset = null);
        return $tags;
    }

    public function retrieveTagsForLabels(array $labels, $limit = null, $offset = null)
    {
        $tagRepo = $this->repository(self::TAG_ENTITY);

        $tags = $tagRepo->findBy(array("label" => $labels), array("displayOrder" => "ASC", "label" => "ASC"), $limit, $offset);
        return $tags;
    }

    public function searchTagLabelsStartingWith($label, $limit = null, $offset = null)
    {
        $eTag = self::TAG_ENTITY;
        $queryString = "SELECT t.label FROM {$eTag} t WHERE t.label LIKE :search_string";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameters(array(
                "search_string" => $label . "%",
            ))
        ;

        $result = $query->getResult();
        if ($result) {
            $result = ipull($result, "label");
        }
        return $result;
    }
}
