<?php
namespace AnhNhan\Converge\Modules\Forum\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use AnhNhan\Converge\Web\Application\JsonPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use League\Fractal;
use AnhNhan\Converge\Modules\Forum\Transform\DiscussionTransformer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionDisplayController extends AbstractForumController
{
    public function process()
    {
        $request = $this->request();
        $accepts = $request->getAcceptableContentTypes();

        foreach ($accepts as $accept) {
            switch ($accept) {
                case 'application/json':
                case 'text/json':
                    return $this->handleJson();
                    break;
                case 'text/html':
                    return $this->handle();
                    break;
            }
        }

        return $this->handle();
    }

    public function fetchData($query = null)
    {
        $request = $this->request;

        $currentId = $request->request->get('id');

        $query = $query ?: $this->buildQuery();
        $disq = $query
            ->retrieveDiscussion('DISQ-' . $currentId)
        ;

        if (!$disq)
        {
            return id(new ResponseHtml404)->setText('Could not find a discussion for \'' . $currentId . '\'');
        }

        $posts = mkey($query->retrivePostsForDiscussion($disq), 'uid');
        $merged = array_merge($posts, [$disq]);
        $merged = flat_map($merged, function ($obj) { return array_merge([$obj], $obj->comments->toArray()); });
        fetch_external_authors($merged, create_user_query($this->externalApp('people')));
        $query->fetchExternalsForDiscussions([$disq]);

        return [
            'disq' => $disq,
            'posts' => $posts,
        ];
    }

    public function handleJson()
    {
        $stopWatch = $this->app()->getService('stopwatch');
        $timer = $stopWatch->start('discussion-listing-json');

        $data = $this->fetchData();
        $disq = $data['disq'];
        $posts = mpull($data['posts'], 'toDictionary');
        $posts = array_map(function ($dict) use ($data)
            {
                if (!$dict['deleted'])
                {
                    $dict['rawText'] = $data['posts'][$dict['uid']]->rawText;
                }
                return $dict;
            }, $posts);

        $tags = mpull(mpull($disq->tags->toArray(), 'tag'), 'toDictionary');
        $tags = ikey($tags, 'uid');

        $fractal = new Fractal\Manager;
        $resource = new Fractal\Resource\Collection([$disq], new DiscussionTransformer($tags));

        $result = $fractal->createData($resource)->toArray();
        $dictDisq = head($result['data']);
        $dictDisq['rawText'] = $disq->rawText;
        $dictDisq['comments'] = mpull($disq->comments->toArray(), 'toDictionary');

        $time = $timer->stop()->getDuration();

        $payload = new JsonPayload();
        $payload->setPayloadContents(array(
            'disq' => $dictDisq,
            'posts' => $posts,
            'toc' => [],
            'time' => $time,
        ));
        return $payload;
    }

    public function handle()
    {
        $request = $this->request;
        $app = $this->app;

        $currentId = $request->request->get('id');

        $data = $this->fetchData();
        if ($data instanceof ResponseHtml404)
        {
            return $data;
        }
        $disq = $data['disq'];
        $posts = $data['posts'];

        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setTitle($disq->label);
        $payload->setPayloadContents($container);

        $grid = grid();
        $row  = $grid->row();
        $disqColumn = $row->column(9)->setId('disq-column');
        $tagColumn  = $row->column(3)->addClass('tag-column');

        $custom_rules = get_custom_markup_rules($this->app->getService('app.list'));
        $tocExtractor = new \AnhNhan\Converge\Modules\Markup\TOCExtractor($custom_rules);
        $tocs = [];
        $markups = [];

        foreach (array_merge($posts, $disq ? [$disq] : []) as $post) {
            list($toc, $markup) = $tocExtractor->parseExtractAndProcess($post->rawText);
            $tocs[$post->uid] = $toc;
            $markups[$post->uid] = $markup;
        }

        $disqPanel = null;
        if ($disq) {
            $disqPanel = renderDiscussion($disq, $markups[$disq->uid])->getProcessed();
            $disqColumn->push($disqPanel);
        }

        foreach ($posts as $post) {
            $markup = $markups[$post->uid];
            $disqColumn->push(renderPost($post, $markup));
        }

        $tocContainer = panel(h2('Table of Contents'), 'forum-toc-affix');
        $tagColumn->push($tocContainer);

        $ulCont = Converge\ht('ul')->addClass('nav forum-toc-nav');
        foreach (array_merge($disq ? [$disq] : [], $posts) as $obj) {
            // Crude, but works
            $is_disq = !method_exists($obj, 'parentDisq');

            $type = $is_disq ? 'Discussion' : 'Post';
            $toc_entry = self::toc_entry($type, $obj->author->name, $obj->uid, $obj->rawText);

            if (!$is_disq && $obj->deleted) {
                $toc_entry = Converge\ht('li',
                    a(Converge\hsprintf('<em>Post</em> deleted'), '#' . hash_hmac('sha512', $obj->uid, time()))
                );
                $ulCont->append($toc_entry);
                continue;
            }

            $subToc = idx($tocs, $obj->uid);
            if ($subToc) {
                $subUl = Converge\ht('ul')->addClass('subtoc');
                foreach ($subToc as $tt) {
                    $subUl->append(Converge\hsprintf(
                        '<li class="subtoc-%s"><a style="padding-left: %fem;" href="#%s">%s</a></li>',
                        $tt['type'],
                        $tt['level'] + 1.5,
                        $tt['hash'],
                        $tt['text']
                    ));
                }

                $toc_entry->append($subUl);
            }

            $ulCont->append($toc_entry);
        }
        $tocContainer->append($ulCont);

        $container->push($grid);

        $payload->resMgr
            ->requireJs('application-forum-toc-affix')
            ->requireJs('application-forum-show-changes')
            ->requireCss('application-forum-discussion-display')
            ->requireCss('application-diff')
        ;

        return $payload;
    }

    private static function shorten_po($text)
    {
        return phutil_utf8_shorten($text, 140);
    }

    private static function toc_link($type, $name, $id)
    {
        return a(
            Converge\hsprintf('%s by <strong>%s</strong>', $type, $name),
            '#' . $id
        );
    }

    private static function toc_entry($type, $name, $id, $text)
    {
        return popover('li', self::toc_link($type, $name, $id), self::shorten_po($text));
    }
}
