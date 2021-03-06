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

    public function retrieveChannels($limit = null, $offset = null)
    {
        return $this
            ->repository(self::ENTITY_CHANNEL)
            ->findBy([], [], $limit, $offset)
        ;
    }

    public function searchArticlesInChannel($channel_id, array $ids, $limit = null, $offset = null)
    {
        $eArticle = self::ENTITY_ARTICLE;
        $queryString = "SELECT art, ch, art_tag, art_author
            FROM {$eArticle} art
                JOIN art.channel ch
                LEFT JOIN art.tags art_tag
                LEFT JOIN art.authors art_author
            WHERE
                (ch.uid = :channel_id OR ch.slug = :channel_id)
            AND
                (art.uid IN (:ids) OR art.slug IN (:ids))
        ";

        $art_id_fun = function ($x)
        {
            return [
                $x,
                'DMAR-' . $x,
            ];
        };

        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameters([
                'channel_id' => $channel_id,
                'ids' => array_mergev(array_map($art_id_fun, $ids)),
            ])
        ;
        return $query->getResult();
    }

    public function retrieveArticles($limit = null, $offset = null)
    {
        $eArticle = self::ENTITY_ARTICLE;
        $queryString = "SELECT art, ch, art_tag, art_author
            FROM {$eArticle} art
                JOIN art.channel ch
                LEFT JOIN art.tags art_tag
                LEFT JOIN art.authors art_author
        ";

        $query = $this->em()
            ->createQuery($queryString)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        return $query->getResult();
    }

    public function fetchExternalsForArticles(array $articles)
    {
        assert_instances_of($articles, self::ENTITY_ARTICLE);

        $authors = mpull(array_filter(mpull($articles, 'authors')), 'toArray');
        $authors = array_mergev($authors);
        fetch_external_authors($authors, $this->requireExternalQuery(self::EXT_QUERY_USER), 'userId', 'setUser', 'user');

        $article_tags = mpull(array_filter(mpull($articles, 'tags')), 'toArray');
        $tags_flat = array_mergev($article_tags);
        $tag_query = $this->requireExternalQuery(self::EXT_QUERY_TAG);
        fetch_external_tags($tags_flat, $tag_query);
    }
}
