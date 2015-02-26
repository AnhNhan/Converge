<?php
namespace AnhNhan\Converge\Modules\Forum\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\Converge\Modules\Forum\Storage\Post;
use AnhNhan\Converge\Modules\Forum\Storage\PostTransaction;
use AnhNhan\Converge\Modules\Forum\Transaction\PostTransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Views\Form\FormView;
use AnhNhan\Converge\Views\Form\Controls\SubmitControl;
use AnhNhan\Converge\Views\Form\Controls\TextAreaControl;
use AnhNhan\Converge\Views\Form\Controls\TextControl;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class PostEditController extends AbstractForumController
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

        $container = new MarkupContainer;
        $payload = $this->payload_html();
        $payload->setPayloadContents($container);
        $query = new DiscussionQuery($this->app);

        $errors = array();

        $disqId = $request->request->get('disq-id');
        $discussion = $query->retrieveDiscussion("DISQ-" . $disqId);

        if (!$discussion) {
            return $payload->setPayloadContents(Converge\ht("h1", "Discussion {$disqId} does not exist"));
        }

        if ($postId = $request->request->get('post-id')) {
            $post = $query->retrievePost("POST-" . $postId);
        } else {
            $post = Post::initializeForDiscussion($discussion);
        }

        $post_text = $post->rawText;
        $draft_key = $discussion->uid . '~post';
        $contents_draft_date = null;
        if (!$post->uid && $requestMethod != 'POST')
        {
            $contents_draft = $this->getDraftObject($draft_key);
            $post_text = $contents_draft ? $contents_draft['contents'] : null;
            $contents_draft_date = $contents_draft ? 'Draft originally loaded on ' . date("h:i - D, d M 'y", $contents_draft['modified_at']) : null;
        }

        if ($requestMethod == "POST") {
            $post_text = trim($request->request->get("text"));
            $post_text = Converge\normalize_newlines($post_text);

            if (empty($post_text)) {
                $errors[] = "Text is empty!";
            }

            if (!$errors) {
                $app = $this->app;
                $em = $app->getEntityManager();

                $editor = PostTransactionEditor::create($em)
                    ->setActor($this->user->uid)
                    ->setEntity($post)
                    ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_ERROR)
                ;

                if (!$post->uid) {
                    $editor->addTransaction(
                        PostTransaction::create(TransactionEntity::TYPE_CREATE, $discussion->uid)
                    );
                }

                $editor
                    ->addTransaction(
                        PostTransaction::create(PostTransaction::TYPE_EDIT_POST, $post_text)
                    )
                ;

                if (!$post->uid)
                {
                    $this->deleteDraftObject($draft_key);
                }

                $xacts = $editor->apply();
                $this->dispatchEvent(Event_PostTransaction_Record, arrayDataEvent($xacts));

                $targetURI = "/disq/" . $discussion->cleanId;
                return new RedirectResponse($targetURI);
            }
        }

        $form = id(new FormView)
            ->setDualColumnMode(false)
            ->setTitle($post->uid ? "Edit post" : sprintf("Post a comment to '%s'", $discussion->label))
            ->setAction($request->getPathInfo())
            ->setMethod("POST")
            ->append(id(new TextAreaControl)
                ->setLabel("Text")
                ->setName("text")
                ->setValue($post_text)
                ->setHelp($contents_draft_date)
                ->addOption('data-draft-key', !$post->uid ? $draft_key : null)
                ->addClass("forum-markup-processing-form")
            )
            ->append(id(new SubmitControl)
                ->addCancelButton("/disq/" . $discussion->cleanId)
                ->addSubmitButton("Hasta la vista!")
            )
            ->append(Converge\ht("div", "Foo")->addClass("markup-preview-output"))
        ;
        $container->push($form);

        $this->app->getService("resource_manager")
            ->requireJs("application-forum-markup-preview");

        return $payload;
    }
}
