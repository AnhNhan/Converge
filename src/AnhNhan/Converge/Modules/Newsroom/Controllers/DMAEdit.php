<?php
namespace AnhNhan\Converge\Modules\Newsroom\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Newsroom\Activity\ArticleRecorder;
use AnhNhan\Converge\Modules\Newsroom\Storage\DumbMarkdownArticle;
use AnhNhan\Converge\Modules\Newsroom\Storage\DMArticleTransaction;
use AnhNhan\Converge\Modules\Newsroom\Transaction\DMAEditor;
use AnhNhan\Converge\Modules\Tag\Views\FormControls\TagSelector;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Views\Form\Controls\SelectControl;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DMAEdit extends ArticleController
{
    public function requiredUserRoles($request)
    {
        return [
            'ROLE_USER',
        ];
    }

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
            $article = new DumbMarkdownArticle;
        }

        $errors = [];

        $e_title = null;
        $e_slug = null;
        $e_byline = null;
        $e_authors = null;
        $e_color = null;
        $e_font = null;
        $e_header_style = null;
        $e_text = null;
        $e_tags = null;

        $channel_id = $request->request->get('channel');
        $channel = head($query->searchChannels([$channel_id]));
        if (!$channel)
        {
            return (new ResponseHtml404)->setText(cv\hsprintf('The channel <em>%s</em> is not the channel you are looking for!', $channel_id));
        }
        $orig_authors = $article->authors ? $article->authors->toArray() : [];
        $user_query = create_user_query($this->externalApp('people'));
        $query->fetchExternalsForArticles([$article]);

        $art_uid = $article->uid;
        $art_title = $article->title;
        $art_slug = $article->slug;
        $art_byline = $article->byline;
        $art_authors = $orig_authors ? mpull(mpull($orig_authors, 'user'), 'name') : [];
        $art_settings = $article->settings;
        $art_text = $article->rawText;
        $art_tags_orig = $article->tags ? mkey(mpull($article->tags->toArray(), 'tag'), 'uid') : [];
        $art_tags = mpull($art_tags_orig, 'label');

        $art_authors_orig = mpull(mpull($orig_authors, 'user'), null, 'canonical_name');

        $draft_key = 'create-article-text-at-channel-' . $channel->slug;
        $contents_draft_date = null;
        if (!$art_uid && $requestMethod != 'POST')
        {
            $contents_draft = $this->getDraftObject($draft_key);
            $art_text = $contents_draft ? $contents_draft['contents'] : null;
            $contents_draft_date = $contents_draft ? 'Draft originally loaded on ' . date("h:i - D, d M 'y", $contents_draft['modified_at']) : null;
        }

        $enum_colors = [
            'midnight-blue' => 'Default (Midnight Blue)',
            'none' => 'None',
            'nephritis' => 'Nephritis',
            'pumpkin' => 'Pumpkin',
            'amethyst' => 'Amethyst',
            'wisteria' => 'Wisteria',
            'greensea' => 'Green Sea',
            'belize-hole' => 'Belize Hole',
            'sunflower' => 'Sunflower',
            'orange' => 'Orange',
            'pomegranate' => 'Pomegranate',
            'alizarin' => 'Alizarin',
            'asbestos' => 'Asbestos',
        ];

        $enum_fonts = [
            'adelle' => 'Adelle',
            'freight-text' => 'Freight Text Pro',
            'open-sans' => 'None (Open Sans)',
        ];

        $enum_headers = [
            'no-header' => 'Default (None)',
            'cool-header' => 'Avant Garde All-Caps',
            'deepshadow' => 'Deep Shadows',
            'elegantshadow' => 'Elegant Shadows',
        ];

        $art_theme_color = $article->get_setting('color');
        $art_theme_font = $article->get_setting('font');
        $art_theme_header = $article->get_setting('header_style');

        if ($requestMethod == 'POST')
        {
            $art_title = trim($request->request->get('title'));
            $art_slug = trim($request->request->get('slug'));
            $art_byline = trim($request->request->get('byline'));

            $art_authors = trim($request->request->get('authors'));
            $art_authors = array_map('to_canonical', explode(',', $art_authors));
            $art_authors = array_unique(array_filter($art_authors));

            // Slugifying for normalization
            $art_theme_color = to_slug(trim($request->request->get('color')));
            $art_theme_font = to_slug(trim($request->request->get('font')));
            $art_theme_header = to_slug(trim($request->request->get('header-style')));

            $art_text = trim($request->request->get('text'));
            $art_text = cv\normalize_newlines($art_text);

            $_tags = $request->request->get("tags");

            if (!strlen($art_slug))
            {
                $art_slug = to_slug($art_title);
            }

            if (!strlen($art_title))
            {
                $errors[] = 'We require a descriptive title';
                $e_title = 'obviously, we need this';
            }

            if (!strlen($art_slug))
            {
                $errors[] = 'A slug needs to be provided';
                $e_slug = 'how could you!?';
            }

            if (!isset($enum_colors[$art_theme_color]))
            {
                $errors[] = cv\hsprintf('We don\'t know about the color \'<em>%s</em>\'', $art_theme_color);
                $e_color = 'invalid';
            }

            if (!isset($enum_fonts[$art_theme_font]))
            {
                $errors[] = cv\hsprintf('We don\'t know about the font \'<em>%s</em>\'', $art_theme_font);
                $e_font = 'invalid';
            }

            if (!isset($enum_headers[$art_theme_header]))
            {
                $errors[] = cv\hsprintf('We don\'t know about the header style \'<em>%s</em>\'', $art_theme_header);
                $e_header_style = 'invalid';
            }

            $art_authors_objects = $user_query->retrieveUsersForCanonicalNames($art_authors);
            $art_authors_objects = mkey($art_authors_objects, 'canonical_name');
            if ($art_authors && count($art_authors_objects) < count($art_authors))
            {
                $errors[] = cv\hsprintf(
                    'Could not find user(s): %s',
                    phutil_implode_html(', ', array_map(
                        function ($x) { return phutil_safe_html(tooltip('span', '@' . $x, 'user not found')->addClass('bad-username')); },
                        array_diff($art_authors, mpull($art_authors_objects, 'canonical_name'))
                    ))
                );
                $e_authors = 'contains imaginary users';
            }

            $tag_result = validate_tags_from_form_input($_tags, $this->externalApp('tag'));
            if (is_array($tag_result))
            {
                $tag_objects = $tag_result;
                $art_tags = mpull($tag_objects, 'label', 'uid');
            }
            else
            {
                $errors[] = $tag_result;
                $e_tags = 'invalid value';
            }

            if (!$errors)
            {
                $art_settings['color'] = $art_theme_color;
                $art_settings['font'] = $art_theme_font;
                $art_settings['header_style'] = $art_theme_header;

                $orig_tag_ids = array_keys($art_tags_orig);
                $curr_tag_ids = array_keys($art_tags);

                $new_tag_ids = array_diff($curr_tag_ids, $orig_tag_ids);
                $del_tag_ids = array_diff($orig_tag_ids, $curr_tag_ids);

                $em = $this->app->getEntityManager();

                $editor = DMAEditor::create($em)
                    ->setActor($this->user->uid)
                    ->setEntity($article)
                    ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_SKIP)
                    ->setFlushBehaviour(TransactionEditor::FLUSH_FLUSH)
                ;

                if (!$article->uid) {
                    $editor
                        ->addTransaction(
                            DMArticleTransaction::create(TransactionEntity::TYPE_CREATE, $art_slug)
                        )
                        ->addTransaction(
                            DMArticleTransaction::create(DMArticleTransaction::TYPE_EDIT_CHANNEL, $channel)
                        )
                    ;
                }

                $editor
                    ->addTransaction(
                        DMArticleTransaction::create(DMArticleTransaction::TYPE_EDIT_TITLE, $art_title)
                    )
                    ->addTransaction(
                        DMArticleTransaction::create(DMArticleTransaction::TYPE_EDIT_BYLINE, $art_byline)
                    )
                    ->addTransaction(
                        DMArticleTransaction::create(DMArticleTransaction::TYPE_EDIT_TEXT, $art_text)
                    )
                    ->addTransaction(
                        DMArticleTransaction::create(DMArticleTransaction::TYPE_EDIT_SETTING, $art_settings)
                    )
                ;

                $author_add = array_diff(array_keys($art_authors_objects), array_keys($art_authors_orig));
                $author_del = array_diff(array_keys($art_authors_orig), array_keys($art_authors_objects));

                if ($author_add || $author_del)
                {
                    foreach (['add' => $author_add, 'del' => $author_del] as $type => $usernames)
                    {
                        $is_add = $type == 'add';
                        $xact_type = $is_add ? DMArticleTransaction::TYPE_ADD_AUTHOR : DMArticleTransaction::TYPE_DEL_AUTHOR;
                        $user_source = $is_add ? $art_authors_objects : $art_authors_orig;
                        foreach (array_select_keys($user_source, $usernames) as $assigned_user)
                        {
                            $editor->addTransaction(
                                DMArticleTransaction::create($xact_type, $assigned_user->uid)
                            );
                        }
                    }
                }

                foreach ($new_tag_ids as $tag_id) {
                    $editor->addTransaction(
                        DMArticleTransaction::create(DMArticleTransaction::TYPE_ADD_TAG, $tag_id)
                    );
                }

                foreach ($del_tag_ids as $tag_id) {
                    $editor->addTransaction(
                        DMArticleTransaction::create(DMArticleTransaction::TYPE_DEL_TAG, $tag_id)
                    );
                }

                $em->beginTransaction();
                try
                {
                    if (!$art_uid)
                    {
                        $this->deleteDraftObject($draft_key);
                    }

                    $recorder = new ArticleRecorder($this->externalApp('activity'));
                    $recorder->record($editor->apply());
                    $em->commit();
                }
                catch (\Doctrine\DBAL\Exception\DuplicateKeyException $e)
                {
                    $em->rollback();
                    $errors[] = cv\safeHtml('Unknown error happened. Maybe an article with this slug already exists. Maybe it was something else. No idea.');
                    goto form_rendering;
                }

                $targetURI = urisprintf('/a/%p/%p', $channel->slug, $article->slug);
                return new RedirectResponse($targetURI);
            }
        }

        form_rendering:

        $page_title = !$art_uid ? 'Create new article' : 'Edit article ' . $art_title;

        $container = new MarkupContainer;

        if ($errors) {
            $panel = panel(h2('Sorry, there had been an error'))
                ->append(cv\ht('p', 'We can\'t continue until these issue(s) have been resolved:'))
            ;
            $list = cv\ht('ul');
            foreach ($errors as $e) {
                $list->append(cv\ht('li', $e));
            }
            $panel->append($list);
            $container->push($panel);
        }

        $color_control = id(new SelectControl)
            ->setLabel('Accent Color')
            ->setName('color')
            ->setHelp('affects headers and links')
            ->setError($e_color)
            ->setSelected($art_theme_color)
        ;
        foreach ($enum_colors as $key => $label)
        {
            $color_control->addEntry([
                'label' => $label,
                'value' => $key,
                'class' => 'color-bg-' . $key,
            ]);
        }

        $font_control = id(new SelectControl)
            ->setLabel('Font')
            ->setName('font')
            ->setHelp('article body font')
            ->setError($e_font)
            ->setSelected($art_theme_font)
        ;
        foreach ($enum_fonts as $key => $label)
        {
            $font_control->addEntry([
                'label' => $label,
                'value' => $key,
                'class' => 'newsroom-font-' . $key,
            ]);
        }

        $header_style_control = id(new SelectControl)
            ->setLabel('Header Style')
            ->setName('header-style')
            ->setHelp('main header style')
            ->setError($e_header_style)
            ->setSelected($art_theme_header)
        ;
        foreach ($enum_headers as $key => $label)
        {
            $header_style_control->addEntry([
                'label' => $label,
                'value' => $key,
                'class' => $key,
            ]);
        }

        if (is_array($art_tags)) {
            $art_tags = implode(", ", $art_tags);
        }

        $tag_selector = id(new TagSelector)
            ->addClass("disq-tag-selector")
            ->setLabel("Tags")
            ->setName("tags")
            ->setValue($art_tags)
            ->setError($e_tags)
        ;

        $form = form($page_title, $request->getPathInfo(), 'POST')
            ->append(form_textcontrol('Title', 'title', $art_title)
                ->setError($e_title)
            )
            ->append(form_textcontrol('Slug', 'slug', $art_slug)
                ->setHelp(cv\safeHtml('can\'t be changed once created; optional'))
                ->setError($e_slug)
                ->addOption('disabled', $art_uid ? 'disabled' : null)
            )
            ->append(form_textcontrol('Byline', 'byline', $art_byline)
                ->setHelp('short one-liner to describe the article')
                ->setError($e_byline)
            )
            ->append(form_textcontrol('Authors', 'authors', implode(', ', $art_authors))
                ->setHelp('comma separated list; optional')
                ->setError($e_authors)
            )
            ->append($tag_selector)
            ->append($color_control)
            ->append($font_control)
            ->append($header_style_control)
            ->append(form_textareacontrol('Text', 'text', $art_text)
                ->setHelp($contents_draft_date ?: 'optional')
                ->setError($e_text)
                ->addOption('data-draft-key', !$art_uid ? $draft_key : null)
                ->addClass('forum-markup-processing-form'))
            ->append(a(cv\icon_ion('formatting help text', 'help-buoy'), 'newsroom/markup/help')->addClass('btn btn-info btn-large pull-right'))
            ->append(form_submitcontrol($art_uid ? 'a/' . $art_slug : '/newsroom/'))
            ->append(h2(cv\hsprintf('Preview <small>(without most styles and colors)</small>')))
            ->append(div('article', div('article-body markup-preview-output', 'Foo')))
        ;

        $container->push($form);

        $payload->resMgr
            ->requireCss('newsroom-pck')
            ->requireCss('application-newsroom-article-page')
            ->requireJs('application-forum-markup-preview')
            ->requireJS("application-forum-tag-selector")
        ;

        $payload = $this->payload_html;
        $payload->setTitle($page_title);
        $payload->setPayloadContents($container);
        return $payload;
    }
}
