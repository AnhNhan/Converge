<?php
namespace AnhNhan\Converge\Modules\Newsroom\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupEngine;
use AnhNhan\Converge\Views\Objects\Listing;
use AnhNhan\Converge\Views\Objects\Object;
use AnhNhan\Converge\Views\Property\PropertyList;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ArticleListing extends ArticleController
{
    public function handle()
    {
        $query = $this->buildArticleQuery();
        $articles = $query->retrieveArticles();
        $query->fetchExternalsForArticles($articles);

        $article_channels = group($articles, function ($article) { return $article->channel->label; });
        $channels = $query->retrieveChannels();

        $container = div('article-listing-container');

        $container
            ->append(
                a('create channel', 'channel/create')
                    ->addClass('btn btn-primary pull-right')
            )
            ->append(h1('Newsroom'))
        ;

        foreach ($channels as $channel)
        {
            $_articles = idx($article_channels, $channel->label, []);
            $panel = panel($header = new MarkupContainer, 'article-listing-channel')
                ->setId($channel->uid)
            ;

            $header_buttons = div('pull-right btn-group article-listing-channel-midriff-buttons')
                ->append(
                    a(cv\icon_ion('edit channel', 'edit'), urisprintf('channel/%p/edit', $channel->slug))
                        ->addClass('btn btn-default ')
                )
                ->append(
                    a(cv\icon_ion('add article', 'plus'), urisprintf('a/%p/create', $channel->slug))
                        ->addClass('btn btn-primary ')
                )
            ;
            $header->push(h2($channel->label, 'pull-left'));
            $header->push($header_buttons);
            $header->push(cv\ht('div')->addClass('clearfix'));

            // mkey as a fancy unique function
            $channel_authors = mkey(array_mergev(pull($_articles, function ($article) { return mpull($article->authors->toArray(), 'user'); })), 'uid');

            $midriff = $panel->midriff();
            if (count($_articles))
            {
                $midriff->push(cv\hsprintf(
                    '<span style="padding: 0 1.5em">%d articles by %d different authors</span>',
                    count($_articles),
                    count($channel_authors)));
                $proplist = new PropertyList;
                $proplist->addEntry('Participating authors', strong(implode_link_user(', ', $channel_authors)));
                $midriff->push($proplist);
            }

            $listing = (new Listing)
                ->setEmptyMessage(cv\hsprintf('No articles in %s', $channel->label))
            ;
            $panel->append($listing);
            foreach ($_articles as $article)
            {
                $article_uri = urisprintf('/a/%p/%p', $article->channel->slug, $article->slug);
                $listing->addObject($object = (new Object)
                    ->setId($article->uid)
                    ->setHeadline(h3($article->title))
                    ->setHeadHref($article_uri)
                    ->setByLine($article->byline ?: cv\ht('em', 'No byline given :/')->addClass('muted'))
                    ->addDetail($article->authors->count()
                        ? strong(implode_link_user(', ', mpull($article->authors->toArray(), 'user')))
                        : span('muted', 'nobody')
                        , 'person-stalker'
                    )
                    ->addDetail($article->modifiedAt->format("D, d M 'y"), 'calendar')
                    ->addAttribute(
                        a(cv\icon_ion('edit article', 'edit'), $article_uri . '/edit')->addClass('btn btn-primary btn-small')
                    )
                );
                $article->tags->count() and $object->addAttribute(implode_link_tag(' ', mpull($article->tags->toArray(), 'tag'), true));
            }

            $container->append($panel);
        }

        $payload = $this->payload_html;
        $payload->setTitle('Article Listing - Converge Newsroom');
        $payload->setPayloadContents($container);
        return $payload;
    }
}
