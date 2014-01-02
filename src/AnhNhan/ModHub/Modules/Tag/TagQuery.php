<?php
namespace AnhNhan\ModHub\Modules\Tag;

use AnhNhan\ModHub\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TagQuery extends Query
{
    const TAG_ENTITY = 'AnhNhan\ModHub\Modules\Tag\Storage\Tag';

    public function retrieveTagsForIDs(array $ids, $limit = null, $offset = null)
    {
        $tagRepo = $this->repository(self::TAG_ENTITY);

        $tags = $tagRepo->findBy(array("id" => $ids), array("displayOrder" => "ASC", "label" => "ASC"), $limit, $offset);
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
}
