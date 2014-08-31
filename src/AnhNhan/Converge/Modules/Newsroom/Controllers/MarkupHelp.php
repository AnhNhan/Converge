<?php
namespace AnhNhan\Converge\Modules\Newsroom\Controllers;

use AnhNhan\Converge as cv;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class MarkupHelp extends ArticleController
{
    public function handle()
    {
        $div = div('article article-color-pumpkin');
        $div->append(div('article-header', h1('Markup Help'))->append(h2(cv\ht('small', 'Never ph34r! Dr. @anhnhan is here to help!'))));

        //$div->append(div('article-detail', 'By ')->append(span('article-author-name', '@anhnhan')));

        $article_body = div('article-body newsroom-font-adelle');
        $div->append($article_body);

        $article_body->append(cv\safeHtml(<<<EOT
<p class="lead-char separator-paragraph">
Here we stand, to learn the monstrosity of the creation sprung from my very own hands.
I shalt teach you the way to tame and subdue that beast. Together we will rise, though you alone
shalt complete the feat. But ph34r not, thou shalt not be alone. I shall aid and hand you this hopefully useful
pergament to help you in this perilious endeavor. Oh, somebody had questions? On with the questions!
</p>

<h4 class="qa-question">Tell me about that ph34rs0m3 markup...</h4>
<p>Excellent question, right from the beginning!</p><p>That markup that we are talking about came out of my obvious desire to add (useless) features for formatting. Along the way it got infected with the nefarious T-Virus and mutilated into its gruesome current form.</p>

<p>Let's start with something simple:</p>

<h1>Basic Formatting H4x0|2 5k1ll5</h1>

<p>Before one becomes 1337, one has to learn the way of l33t. Watch and learn!</p>

<pre><code>Hello, this is some *Markdown*. Make some **bold** text. Or write some `code`.

Links:
 - [Google](http://google.com/)
 - [Microsoft](http://microsoft.com)
 - [Converge Newsroom](newsroom/)

Add in some other paragraph. Two line breaks, and you are set!
This way, single line breaks can act as a continuation of the previous sentence.
That's cool, hm?

 * * *

Who put that line in the way!?</code></pre>

<p>The result is full of ph34r. See!</p>

<blockquote style="background: none; color: black;">
    <p>Hello, this is some <em>Markdown</em>. Make some <strong>bold</strong> text. Or write some <code>code</code>.

    <p>Links</p>

    <ul>
        <li><a href="http://google.com/">Google</a></li>
        <li><a href="http://microsoft.com/">Microsoft</a></li>
        <li><a href="newsroom/">Converge Newsroom</a></li>
    </ul>

    <p>Add in some other paragraph. Two line breaks, and you are set!
    This way, single line breaks can act as a continuation of the previous sentence.
    That's cool, hm?</p>

    <hr />

    <p>Who put that line in the way!?</p>
</blockquote>

<p>If you are using the <em>Adelle</em> font (that very thin font with serifs), then bold and italic text will look the same. Technical reasons (I have no idea where I put the straight bold font).</p>

<h1>User Mentions & Tags</h1>

<pre><code>Hey, @anhnhan. Behold the #dev-only tag!
</code></pre>

<blockquote style="background: none; color: black;">
    <p>Hey, <strong><a title="@anhnhan" class="user-link user-mention" href="u/anhnhan" data-toggle="tooltip" backbone="">An H. N. Han</a></strong>. Behold the <a data-original-title="Excludes moderators etc.." backbone="" href="tag/75a4nxasakj6kg54" class="tag-link hashtag" data-toggle="tooltip" title=""><span class="tag-object tag-color-restricted">dev-only</span></a> tag!</p>
</blockquote>

<p>Note that hashtags may clash with header syntax. If you only see a header in the preview, just prepend a backslash (have one for copy-paste here: <code>\</code>), then everything should work out.</p>

<h1>The lead character</h1>

<pre><code>{{lead-char =
    Qui in placeat iusto facilis distinctio est odit. Harum iste occaecati repellendus. Natus beatae inventore aspernatur necessitatibus omnis. Quasi enim dolor qui repellendus officiis praesentium explicabo. Id minus voluptas qui quo omnis iure adipisci. Nisi in in et aut laboriosam fugit id. Neque impedit aut nesciunt nostrum. Beatae sed ullam temporibus est amet ducimus neque. Qui et sunt neque ut. Error modi ullam hic odit libero nostrum. Mollitia omnis in in rerum placeat aut. Sunt voluptatibus delectus qui qui quidem corporis. Soluta quae dicta qui dolorem.
}}
</code></pre>

<blockquote style="background: none; color: black;">
    <p class="lead-char">Qui in placeat iusto facilis distinctio est odit. Harum iste occaecati repellendus. Natus beatae inventore aspernatur necessitatibus omnis. Quasi enim dolor qui repellendus officiis praesentium explicabo. Id minus voluptas qui quo omnis iure adipisci. Nisi in in et aut laboriosam fugit id. Neque impedit aut nesciunt nostrum. Beatae sed ullam temporibus est amet ducimus neque. Qui et sunt neque ut. Error modi ullam hic odit libero nostrum. Mollitia omnis in in rerum placeat aut. Sunt voluptatibus delectus qui qui quidem corporis. Soluta quae dicta qui dolorem.</p>
</blockquote>

<p>Please note that you need to have the whole paragraph wrapped inside. Only works with one paragraph. Do not include multiple paragraphs or anything that resembles multiple paragraphs inside. Please do not combine with the separator paragraph, results will be unspeakable. We have a special case for that.</p>

<h1>The separator paragraph</h1>

<pre><code>{{separator-paragraph =
    Neque impedit aut nesciunt nostrum. Beatae sed ullam temporibus est amet ducimus neque. Qui et sunt neque ut.
}}
</code></pre>

<blockquote style="background: none; color: black;">
    <p class="separator-paragraph">Neque impedit aut nesciunt nostrum. Beatae sed ullam temporibus est amet ducimus neque. Qui et sunt neque ut.</p>
</blockquote>

<p>Behold that line above this paragraph. Such is the might of the separator paragraph.</p>

<p>Note that you need to have the whole paragraph wrapped inside. It only works on a single paragraph. Do not include multiple paragraphs or anything that resembles multiple paragraphs inside. Please do not combine with the lead character. Unspeakable things will befall upon you.</p>

<h1>The lead character + separator paragraph</h1>

<pre><code>{{lead-char-sep-paragraph =
    Neque impedit aut nesciunt nostrum. Beatae sed ullam temporibus est amet ducimus neque. Qui et sunt neque ut.
}}
</code></pre>

<blockquote style="background: none; color: black;">
    <p class="lead-char separator-paragraph">Neque impedit aut nesciunt nostrum. Beatae sed ullam temporibus est amet ducimus neque. Qui et sunt neque ut.</p>
</blockquote>

<p>Ohhhhhhh! Such ph34r! We are close, minion!</p>

<h1>Only the asking shall be rewarded</h1>

<pre><code>{{qa-question-h4= What's your question? }}

{{qa-answerer = @anhnhan}} Error modi ullam hic odit libero nostrum. Mollitia omnis in in rerum placeat aut. Sunt voluptatibus delectus qui qui quidem corporis. Soluta quae dicta qui dolorem.
</code></pre>

<blockquote style="background: none; color: black;">
    <h4 class="qa-question">What's your question?</h4>
    <p><strong class="qa-answerer"><a title="@anhnhan" class="user-link user-mention" href="u/anhnhan" data-toggle="tooltip" backbone="">An H. N. Han</a></strong> Error modi ullam hic odit libero nostrum. Mollitia omnis in in rerum placeat aut. Sunt voluptatibus delectus qui qui quidem corporis. Soluta quae dicta qui dolorem.</p>
</blockquote>

<p>Use <code>qa-answerer</code> for names and such. A colon will automatically be added.</p>

<h1>Soundcloud Track Embedding</h1>

<pre><code>{{soundcloud-embed-track = 127544061}}
</code></pre>

<blockquote style="background: none; color: black;">
    <p><iframe width="100%" height="166" src="https://w.soundcloud.com/player/?url=https://api.soundcloud.com/tracks/127544061" frameborder="no" scrolling="no"></iframe></p>
</blockquote>

<p>Let me tell you that you will get an url like <code>https://api.soundcloud.com/tracks/127544061</code>. Please only put in <em>only</em> the <code>127544061</code> part. Or else there be dragons.
</p>
EOT
        ));

        $this->resMgr
            ->requireCss('newsroom-pck')
            ->requireCss('application-newsroom-article-page')
        ;

        $payload = $this->payload_html;
        $payload->setTitle('Markup Help - Converge Newsroom');
        $payload->setPayloadContents($div);
        return $payload;
    }
}
