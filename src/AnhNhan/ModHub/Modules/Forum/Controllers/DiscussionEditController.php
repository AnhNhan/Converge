<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
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
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionEditController extends AbstractForumController
{
    private $defaultLabelValue = "Something pretty descriptive, like 'I want cheezburgrs!'";
    private $defaultTextValue  = "Tell us more about your favourite Pokémon!";

    public function handle()
    {
        $request = $this->request();
        $requestMethod = $request->getMethod();

        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);

        $errors = array();

        if ($requestMethod == "POST") {
            $label = trim($request->request->get("label"));
            $text = trim($request->request->get("text"));

            if (!$errors) {
                $app = $this->app();
                $em = $app->getEntityManager();

                $discussion = new Discussion();

                $editor = DiscussionTransactionEditor::create($em)
                    ->setActor(\AnhNhan\ModHub\Storage\Types\UID::generate("USER"))
                    ->setEntity($discussion)
                    ->addTransaction(
                        DiscussionTransaction::create(TransactionEntity::TYPE_CREATE)
                    )
                    ->addTransaction(
                        DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_LABEL, $label)
                    )
                    ->addTransaction(
                        DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_TEXT, $text)
                    )
                ;

                $editor->apply();

                $targetURI = "/disq/" . preg_replace("/^(.*?-)/", "", $discussion->uid());
                return new RedirectResponse($targetURI);
            }
        }

        $form = new FormView;
        $form
            ->setTitle("New discussion")
            ->setAction("/disq/create")
            ->setMethod("POST");

        $form->append(id(new TextControl())
            ->setLabel("Label")
            ->setName("label")
            ->setValue($this->defaultLabelValue));

        $form->append(id(new TextAreaControl())
            ->setLabel("Text")
            ->setName("text")
            ->setValue($this->defaultTextValue));

        $form->append(id(new SubmitControl())
            ->addCancelButton("/")
            ->addSubmitButton("Hasta la vista!"));

        $container->push($form);

        return $payload;
    }
}
