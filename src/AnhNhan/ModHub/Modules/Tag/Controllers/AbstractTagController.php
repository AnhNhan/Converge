<?php
namespace AnhNhan\ModHub\Modules\Tag\Controllers;

use AnhNhan\ModHub\Modules\Tag\Storage\Tag;
use AnhNhan\ModHub\Web\Application\BaseApplicationController;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class AbstractTagController extends BaseApplicationController
{
    const TAG_ENTITY = 'AnhNhan\ModHub\Modules\Tag\Storage\Tag';

    protected function getRepository($entityName)
    {
        $app = $this->app();
        $entityManager = $app->getEntityManager();
        return $entityManager->getRepository($entityName);
    }

    protected function retrieveTagsForIDs(array $ids, $limit = null, $offset = null)
    {
        $tagRepo = $this->getRepository(self::TAG_ENTITY);

        $tags = $tagRepo->findBy(array("id" => $ids), array("displayOrder" => "ASC", "label" => "ASC"), $limit, $offset);
        return $tags;
    }

    protected function retrieveTags($limit = null, $offset = null)
    {
        $tagRepo = $this->getRepository(self::TAG_ENTITY);

        $tags = $tagRepo->findBy(array(), array("displayOrder" => "ASC", "label" => "ASC"), $limit = null, $offset = null);
        return $tags;
    }

    protected function retrieveTagsForLabels(array $labels, $limit = null, $offset = null)
    {
        $tagRepo = $this->getRepository(self::TAG_ENTITY);

        $tags = $tagRepo->findBy(array("label" => $labels), array("displayOrder" => "ASC", "label" => "ASC"), $limit, $offset);
        return $tags;
    }

    protected function toDictionary(Tag $tag)
    {
        return array(
            "uid"          => $tag->uid(),
            "label"        => $tag->label(),
            "color"        => $tag->color(),
            "displayOrder" => $tag->displayOrder(),
            "description"  => $tag->description(),
        );
    }
}
