<?php
namespace AnhNhan\Converge\Modules\Newsroom\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupEngine;
use AnhNhan\Converge\Modules\Newsroom\Storage\DumbMarkdownArticle;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DMADisplay extends ArticleController
{
    public function handle()
    {
        $request = $this->request;
        $requestMethod = $request->getMethod();
        $query = $this->buildArticleQuery();

        $article = $this->retrieveArticleObject($request, $query);
        if ($article && !($article instanceof DumbMarkdownArticle))
        {
            throw new \Exception('Unkown type of article. Please contact the developers!');
        }
        else if (!$article)
        {
            return (new ResponseHtml404)->setText('This is not the article you are looking for.');
        }
        $authors = $article->authors ? $article->authors->toArray() : [];
        $user_query = create_user_query($this->externalApp('user'));
        fetch_external_authors($authors, $user_query, 'userId', 'setUser', 'user');

        $article_color = $article->get_setting('color');
        $article_font = $article->get_setting('font');

        $page_title = $article->title . ' - Converge Newsroom';

        $container = new MarkupContainer;
        $article_container = div('article')
            ->addClass('article-color-' . $article_color)
        ;
        $container->push($article_container);

        $article_header = div('article-header', h1($article->title)->addClass($article->get_setting('header_style')));
        $article_container->append($article_header);

        $article_detail = div('article-detail', cv\hsprintf(
            'Written by %s in <span class="article-channel-name">%s</span>',
            $authors ? phutil_implode_html(', ', array_map(function ($user)
                {
                    return phutil_safe_html('<span class="article-author-name"><strong>' . link_user($user->user) . '</strong></span>');
                }, $authors))
                : 'nobody',
            $article->channel->label
        ));
        $article_container->append($article_detail);

        $custom_rules = get_custom_markup_rules($this->app->getService('app.list'));

        $article_body = div('article-body')
            ->addClass('newsroom-font-' . $article_font)
        ;
        $article_container->append($article_body);

        if ($byline = $article->byline)
        {
            $article_body->append(cv\hsprintf('<h1><small>%s</small></h1>', $byline));
        }

        $text = MarkupEngine::fastParse($article->rawText, $custom_rules);
        $article_body->append(cv\safeHtml($text));

        $this->resMgr
            ->requireCss('newsroom-pck')
            ->requireCss('application-newsroom-article-page')
        ;

        $payload = $this->payload_html;
        $payload->setTitle($page_title);
        $payload->setPayloadContents($container);
        return $payload;
    }
}
