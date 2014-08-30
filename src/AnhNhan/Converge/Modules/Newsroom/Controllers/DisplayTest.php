<?php
namespace AnhNhan\Converge\Modules\Newsroom\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Grid\Grid;
use AnhNhan\Converge\Views\Panel\Panel;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DisplayTest extends ArticleController
{
    public function handle()
    {
        $payload = new HtmlPayload;
        $payload->setTitle('Article Display Test');
        $container = new MarkupContainer;
        $payload->setPayloadContents($container);

        $user_query = create_user_query($this->externalApp('user'));
        $user  = head($user_query->retrieveUsersForCanonicalNames(['anhnhan'], 1));
        $author_link = strong(link_user($user));

        $articleContainer = div('article article-color-default');
        $container->push($articleContainer);

        $articleContainer->append(cv\safeHtml(<<<EOT
<div class="article-header">
<h1 class="cool-header">Even more activity stream functionality</h1>
</div>
<div class="article-detail">
    Written by <span class="article-author-name">$author_link</span> in <span class="article-channel-name">Anh Nhan's Column</span>
</div>
<div class="article-body newsroom-font-freight-text">
    <p><a href="task/implementactivitystream">Actual activity stream functionality is done</a>. We record them, and we render them nicely (even on user profile pages :)). There's still lots to do, though.</p>
    <h1>Activity Filtering</h1>
    <h2>Situation</h2>
    <p>Currently, we are displaying every single activity from every single user on <a href="activity/">activity/</a>. This will get crowded really fast, especially when a dozen people or more are going to use Converge daily.</p>
    <h2>Approaches</h2>
    <p>There's always the option of going the way of followers / friends and filtering out everything you don't follow / aren't friends with, akin to Twitter and Facebook. The problem with this, though, is that this won't really scale beyond a few dozen concurrent users (with live-computing the data on-demand), which we are probably going to reach very fast.</p>
    <p>We can also go a similar way like the dashboard, filtering by (multi-)tag set memberships. Though I fear that the vast amount of activity will even crumble this approach under its weight.</p>
    <h2>Content &amp; User Curation</h2>
    <p>One approach may facilitate the problem a little bit, though. It will have an impact on performance, and in the end me may have to consider an bringing up a regular upfront expense of computational power anyway for this.</p>
    <p>We can curate users and maybe even content, to rank entries in the activity stream.</p>
    <h3>Sources of Curation</h3>
    <h4>Content Upvotes</h4>
    <p>I'm on Reddit. This actually only promotes preferred / hit-the-meme content. 'Nuff said.</p>
    <h4>User Reputation</h4>
    <p>This is similar to upvotes. This only promotes (very) active users. Also, since mutual friends may give each other good reputation on a regular basis, this would also support nepotism. Now, I'm a communist, but this is obviously going way too far.</p>
    <h4>User Roles</h4>
    <p>One reasonable way to curate users is to look at the roles they inhabit. The higher, the more probable is that they do something important.</p>
    <p>Best example would be when the Kraken consumes a piece of content, it's likely that it's going to be interesting for both others in the team(s), as well as for the casual mortals.</p>
    <h4>User Celebrity Status</h4>
    <p>We might also consider having the staff pick out users and mark them as celebrities / interesting persons.</p>
    <p>We might even consider this as a source of revenue, allowing people to curate other people &amp; content with celebrity status in exchange for some money :).</p>
</div>
EOT
        ));

        $this->resMgr
            ->requireCss('newsroom-pck')
            ->requireCss('application-newsroom-article-page')
        ;

        return $payload;
    }
}
