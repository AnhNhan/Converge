<?php
namespace AnhNhan\Converge\Modules\Forum\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionDisplayController extends AbstractForumController
{
    public function handle()
    {
        $request = $this->request;
        $app = $this->app;

        $currentId = $request->request->get('id');

        $query = $this->buildQuery();
        $disq = $query
            ->retrieveDiscussion('DISQ-' . $currentId)
        ;

        if (!$disq)
        {
            return id(new ResponseHtml404)->setText('Could not find a discussion for \'' . $currentId . '\'');
        }

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

        $page_nr = 1;
        $page_size = 30;

        if ($request->request->has('page-nr') && ($r_page_nr = $request->request->get('page-nr')) && preg_match('/^\\d+$/', $r_page_nr)) {
            $page_nr = $r_page_nr;
        }

        $offset = ($page_nr - 1) * $page_size;

        $posts = mkey($query->retrivePostsForDiscussion($disq), 'uid');
        $query->fetchExternalUsers(array_merge($posts, [$disq]));
        $query->fetchExternalsForDiscussions([$disq]);


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
        $tocContainer->addClass('forum-toc-affix');
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

        $this->resMgr
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
            Converge\hsprintf('<em>%s</em> by <strong>%s</strong>', $type, $name),
            '#' . $id
        );
    }

    private static function toc_entry($type, $name, $id, $text)
    {
        return popover('li', self::toc_link($type, $name, $id), self::shorten_po($text));
    }
}
