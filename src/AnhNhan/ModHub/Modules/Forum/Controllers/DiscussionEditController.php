<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\ModHub\Modules\Forum\Storage\Post;
use AnhNhan\ModHub\Modules\Forum\Transaction\DiscussionTransactionEditor;
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
final class DiscussionEditController extends AbstractForumController
{
    public function handle()
    {
        $request = $this->request;
        $requestMethod = $request->getMethod();

        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);

        $errors = array();

        $query = $this->buildQuery();

        if ($disqId = $request->request->get('id')) {
            $discussion = $query->retrieveDiscussion("DISQ-" . $disqId);

            if (!$discussion) {
                return $payload->setPayloadContents(ModHub\ht("h1", "Discussion does not exist"));
            }

            $query->fetchExternalsForDiscussions(array($discussion));
        } else {
            $discussion = new Discussion;
        }

        if ($requestMethod == "POST") {
            $label = trim($request->request->get("label"));
            $text = trim($request->request->get("text"));

            if (!$errors) {
                $app = $this->app;
                $em = $app->getEntityManager();

                $editor = DiscussionTransactionEditor::create($em)
                    ->setActor(\AnhNhan\ModHub\Storage\Types\UID::generate("USER"))
                    ->setEntity($discussion)
                ;

                if (!$disqId) {
                    $editor->addTransaction(
                        DiscussionTransaction::create(TransactionEntity::TYPE_CREATE)
                    );
                }

                $editor
                    ->addTransaction(
                        DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_LABEL, $label)
                    )
                    ->addTransaction(
                        DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_TEXT, $text)
                    )
                ;

                $editor->apply();

                $targetURI = "/disq/" . $discussion->cleanId;
                return new RedirectResponse($targetURI);
            }
        }

        $form = new FormView;
        $form
            ->setTitle("New discussion")
            ->setAction($request->getPathInfo())
            ->setMethod("POST");

        $form->append(id(new TextControl)
            ->setLabel("Label")
            ->setName("label")
            ->setValue($discussion->label));

        $form->append(id(new TextAreaControl)
            ->addClass("forum-markup-processing-form")
            ->setLabel("Text")
            ->setName("text")
            ->setValue($discussion->text));

        $form->append(id(new SubmitControl)
            ->addCancelButton("/disq/" . ($discussion->cleanId ?: null))
            ->addSubmitButton("Hasta la vista!"));

        $container->push($form);
        $container->push(ModHub\ht("div", "Foo")->addClass("markup-preview-output"));

        $this->app->getService("resource_manager")
            ->requireJs("application-forum-markup-preview");

        return $payload;
    }
}
