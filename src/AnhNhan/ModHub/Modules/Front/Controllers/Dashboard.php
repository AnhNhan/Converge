<?php
namespace AnhNhan\ModHub\Modules\Front\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\ModHub\Modules\Forum\Views\Objects\PaneledForumListing;
use AnhNhan\ModHub\Modules\Tag\Views\TagView;
use AnhNhan\ModHub\Views\Grid\Grid;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use AnhNhan\ModHub\Web\Application\BaseApplicationController;

use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Doctrine\ORM\Query as DoctrineQuery;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class Dashboard extends BaseApplicationController
{
    public function handle()
    {
        $container = new MarkupContainer();
        $container->push(h1('Dashboard'));

        $dash_panel_tags = [
            ['caw', 'sotp'],
            ['staff-only', 'sotp'],
            ['staff-only', 'caw'],
            ['bug', 'caw'],
            ['bug', 'sotp'],
            ['homefront', 'playtesting'],
            ['caw', 'unsc'],
            ['news', 'caw'],
            ['news', 'sotp'],
            ['news', 'homefront'],
        ];

        $grid = new Grid;
        $grid->setId('dashboard-grid');
        $row = $grid->row();
        $container->push($grid);

        $big_tags = mpull($this->lookup_ids_for_tag_names(array_mergev($dash_panel_tags)), null, 'label');
        $big_disqs = [];

        $dash_panel_tags2 = [];
        foreach ($dash_panel_tags as $tag_set)
        {
            $tags = array_values(array_select_keys($big_tags, $tag_set));
            $disq_ids = $this->get_disq_ids_for_tags(mpull($tags, 'uid'));
            $dash_panel_disqs[] = $disq_ids;
        }

        $forum_query = $this->buildForumQuery();
        $big_disqs = mpull($this->fetchDiscussions(array_mergev($dash_panel_disqs), null, null, $forum_query), null, 'uid');
        $forum_query->fetchExternalsForDiscussions($big_disqs);
        $post_counts = $forum_query->fetchPostCountsForDiscussions(array_values($big_disqs));

        foreach ($dash_panel_tags as $index => $tag_set)
        {
            $tags = array_values(array_select_keys($big_tags, $tag_set));
            $result = array_select_keys($big_disqs, $dash_panel_disqs[$index]);
            $panelForumListing = id(new PaneledForumListing)
                ->setTitle(ModHub\ht('h3', 'Forum Listing'))
            ;
            foreach ($tags as $t)
            {
                $panelForumListing->addTag($t);
            }

            foreach ($result as $discussion) {
                $object = new ForumObject;
                $object
                    ->setHeadline($discussion->label)
                    ->setHeadHref("/disq/" . $discussion->cleanId)
                    ->postCount(idx($post_counts, $discussion->uid)["postcount"]);

                $tags = mpull($discussion->tags->toArray(), "tag");
                $tags = msort($tags, "label");
                $tags = array_reverse($tags);
                $tags = msort($tags, "displayOrder");
                foreach ($tags as $tag) {
                    if (!empty($tag)) {
                        $object->addTag(new TagView($tag->label, $tag->color));
                    }
                }

                $object->addDetail($discussion->lastActivity->format("D, d M 'y"));
                $object->addDetail($discussion->authorId);

                $panelForumListing->addObject($object);
            }

            $row->column(6)->push($panelForumListing);
        }

        $payload = new HtmlPayload();
        $payload->setPayloadContents($container);
        return $payload;
    }

    private function lookup_ids_for_tag_names(array $names)
    {
        $tagApp = $this->externalApp('tag');
        $tagEm  = $tagApp->getEntityManager();
        return $tagEm->createQuery('SELECT t FROM AnhNhan\ModHub\Modules\Tag\Storage\Tag t WHERE t.label IN (?0) ORDER BY t.displayOrder ASC, t.label ASC')
            ->setParameters([$names])
            ->getResult()
        ;
    }

    private function get_disq_ids_for_tags(array $tids, $limit = null)
    {
        $request = new Request;
        $request->server->set('REQUEST_URI', 'search/disq/by-tag');
        $request->query->set('tid_inc', $tids);
        $request->query->set('limit', $limit);

        $kernel = $this->app->getService('http_kernel');
        $response = $kernel->handle($request);
        $contents = (array) json_decode($response->getContent());
        assert($contents['status'] == 'ok');
        return $contents['payloads'];
    }

    private function buildForumQuery()
    {
        $query = new DiscussionQuery($this->externalApp('forum'));
        $query->addExternalQueryFromApplication(DiscussionQuery::EXT_QUERY_TAG, $this->externalApp('tag'));
        $query->addExternalQueryFromApplication(DiscussionQuery::EXT_QUERY_USER, $this->externalApp('user'));
        return $query;
    }

    private function fetchDiscussions(array $disq_ids, $limit = 10, $offset = null, DiscussionQuery $query = null)
    {
        $query = $query ?: $this->buildForumQuery();
        $disqs = $query->retrieveDiscussionForIDs($disq_ids, $limit, $offset);
        return $disqs;
    }
}
