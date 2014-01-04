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

        $violations = null;

        if ($requestMethod == "POST") {
            $label = trim($request->request->get("label"));
            $text = trim($request->request->get("text"));
            $validatorInput = array(
                "label" => $label,
                "text"  => $text,
            );
            $violations = $this->validateDiscussion($validatorInput);

            if (!$violations->count()) {
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

        if ($violations && $violations->count()) {
            $panel = new \AnhNhan\ModHub\Views\Panel\Panel;
            $panel->setColor("info");
            $panel->setHeader(ModHub\ht("h3", "I'm sorry, but we can't continue until these issues have been resolved!"));
            $midriff = $panel->midriff();
            $midriff->push(ModHub\ht("span", "Our subtile watchdog ninjas have detected "));
            $midriff->push(ModHub\ht("strong", $violations->count() . " issues"));

            $violationContainer = ModHub\ht("ul");
            foreach ($violations as $violation) {
                $violationContainer->appendContent(ModHub\ht("li", $violation->getMessage()));
                $violationContainer->appendContent($violation->getPropertyPath());
            }
            $panel->append($violationContainer);

            $container->push($panel);
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

    private function validateDiscussion(array $input)
    {
        $validator = Validation::createValidator();
        return $validator->validateValue($input, $this->getValidatorConstraintsForDiscussion());
    }

    private function getValidatorConstraintsForDiscussion()
    {
        $constraints = new Assert\Collection(array(
            "label" => array(
                new Assert\NotEqualTo(array("value" => $this->defaultLabelValue)),
                new Assert\NotBlank,
            ),
            "text" => array(
                new Assert\NotEqualTo(array("value" => $this->defaultTextValue)),
                new Assert\NotBlank,
            ),
        ));
        return $constraints;
    }
}
