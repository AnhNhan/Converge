<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\ModHub\Modules\Forum\Storage\Post;
use AnhNhan\ModHub\Modules\Forum\Storage\PostTransaction;
use AnhNhan\ModHub\Modules\Forum\Transaction\PostTransactionEditor;
use AnhNhan\ModHub\Storage\Transaction\TransactionEditor;
use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;
use AnhNhan\ModHub\Views\Form\FormView;
use AnhNhan\ModHub\Views\Form\Controls\SubmitControl;
use AnhNhan\ModHub\Views\Form\Controls\TextAreaControl;
use AnhNhan\ModHub\Views\Form\Controls\TextControl;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class PostEditController extends AbstractForumController
{
    public function handle()
    {
        $request = $this->request;
        $requestMethod = $request->getMethod();

        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);
        $query = new DiscussionQuery($this->app);

        $errors = array();

        $disqId = $request->request->get('disq-id');
        $discussion = $query->retrieveDiscussion("DISQ-" . $disqId);

        if (!$discussion) {
            return $payload->setPayloadContents(ModHub\ht("h1", "Discussion {$disqId} does not exist"));
        }

        if ($postId = $request->request->get('post-id')) {
            $post = $query->retrievePost("POST-" . $postId);
        } else {
            $post = Post::initializeForDiscussion($discussion);
        }

        if ($requestMethod == "POST") {
            $text = trim($request->request->get("text"));

            if (empty($text)) {
                $errors[] = "Text is empty!";
            }

            if (!$errors) {
                $app = $this->app;
                $em = $app->getEntityManager();

                $editor = PostTransactionEditor::create($em)
                    ->setActor(\AnhNhan\ModHub\Storage\Types\UID::generate("USER"))
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
                        PostTransaction::create(PostTransaction::TYPE_EDIT_POST, $text)
                    )
                ;

                $editor->apply();

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
                ->setValue($post->rawText)
                ->addClass("forum-markup-processing-form")
            )
            ->append(id(new SubmitControl)
                ->addCancelButton("/disq/" . $discussion->cleanId)
                ->addSubmitButton("Hasta la vista!")
            )
            ->append(ModHub\ht("div", "Foo")->addClass("markup-preview-output"))
        ;
        $container->push($form);

        $this->app->getService("resource_manager")
            ->requireJs("application-forum-markup-preview");

        return $payload;
    }
}
