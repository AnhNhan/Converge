<?php
namespace AnhNhan\Converge\Modules\Newsroom\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Newsroom\Activity\ChannelRecorder;
use AnhNhan\Converge\Modules\Newsroom\Storage\ChannelTransaction;
use AnhNhan\Converge\Modules\Newsroom\Transaction\ChannelEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ChannelEdit extends ChannelController
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

        $channel = $this->retrieveChannelObject($request, $query);
        if (!$channel)
        {
            return (new ResponseHtml404)->setText('This is not the channel you are looking for.');
        }

        $errors = [];
        $e_label = null;
        $e_slug = null;

        $channel_uid = $channel->uid;
        $channel_label = $channel->label;
        $channel_slug = $channel->slug;

        $title = $channel_uid ? 'Edit channel \'' . $channel_label . '\'' : 'Create channel';

        $payload = $this->payload_html;
        $payload->setTitle($title);
        $container = new MarkupContainer;
        $payload->setPayloadContents($container);

        if ($requestMethod == 'POST')
        {
            $channel_label = trim($request->request->get('label'));

            $channel_slug = trim($request->request->get('slug'));
            $channel_slug = to_slug($channel_slug);

            if (!strlen($channel_slug))
            {
                $channel_slug = to_slug($channel_label);
            }

            if (!strlen($channel_label))
            {
                $errors[] = 'We require a descriptive label';
                $e_label = 'Better don\'t leave this empty';
            }

            if (!strlen($channel_slug))
            {
                $errors[] = 'A slug needs to be provided';
                $e_slug = 'Should not be empty';
            }

            $exist_object = head($query->searchChannels([$channel_slug]));
            if (!$channel_uid && $exist_object)
            {
                $errors[] = 'Channel with slug already exists';
                $e_slug = 'Already in use';
            }

            if (!$errors)
            {
                $em = $this->app->getEntityManager();

                $editor = ChannelEditor::create($em)
                    ->setActor($this->user->uid)
                    ->setEntity($channel)
                    ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_SKIP)
                ;

                if (!$channel_uid)
                {
                    $editor->addTransaction(
                        ChannelTransaction::create(TransactionEntity::TYPE_CREATE, $channel_slug)
                    );
                }

                $editor
                    ->addTransaction(
                        ChannelTransaction::create(ChannelTransaction::TYPE_EDIT_LABEL, $channel_label)
                    )
                ;

                $recorder = new ChannelRecorder($this->externalApp('activity'));
                $recorder->record($editor->apply());

                $target_uri = '/newsroom/';
                return new RedirectResponse($target_uri);
            }
        }

        if ($errors) {
            $panel = panel()
                ->setHeader(cv\ht('h2', 'Sorry, there had been an error'))
                ->append(cv\ht('p', 'We can\'t continue until these issue(s) have been resolved:'))
            ;
            $list = cv\ht('ul');
            foreach ($errors as $e) {
                $list->append(cv\ht('li', $e));
            }
            $panel->append($list);
            $container->push($panel);
        }

        $form = form($title, $request->getPathInfo(), 'POST')
            ->append(form_textcontrol('Label', 'label', $channel_label)
                ->setError($e_label)
                ->addOption('placeholder', 'A Great Journey')
            )
            ->append(form_textcontrol('Slug', 'slug', $channel_slug)
                ->setError($e_slug)
                ->setHelp(cv\safeHtml('the identifier we use in urls, e.g. <code>a/a-great-journey/article-name-here</code><br />usually, copy-paste the label here; we\'ll do the rest<br />can\'t be changed once created'))
                ->addOption('disabled', $channel_uid ? 'disabled' : null)
                ->addOption('placeholder', 'a-great-journey')
            )
            ->append(form_submitcontrol('newsroom/#' . $channel_uid))
        ;
        $container->push($form);

        return $payload;
    }
}
