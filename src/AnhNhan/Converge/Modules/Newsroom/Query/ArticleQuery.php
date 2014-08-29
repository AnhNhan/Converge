<?php
namespace AnhNhan\Converge\Modules\Newsroom\Query;

use AnhNhan\Converge\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ArticleQuery extends Query
{
    const ENTITY_ARTICLE = 'AnhNhan\Converge\Modules\Newsroom\Storage\Article';
    const ENTITY_CHANNEL = 'AnhNhan\Converge\Modules\Newsroom\Storage\Channel';

    public function searchChannels(array $ids, $limit = null, $offset = null)
    {
        $eChannel = self::ENTITY_CHANNEL;
        $queryString = "SELECT ch FROM {$eChannel} ch WHERE ch.uid IN (:ids) OR ch.slug IN (:ids)";
        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameters(['ids' => array_mergev(array_map(function ($x) { return [$x, 'CHAN-' . $x]; }, $ids))])
        ;
        return $query->getResult();
    }
}
