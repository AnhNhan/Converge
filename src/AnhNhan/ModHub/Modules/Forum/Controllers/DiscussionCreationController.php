<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Modules\Forum\Storage\Post;
use AnhNhan\ModHub\Views\Form\FormView;
use AnhNhan\ModHub\Views\Form\Controls\SubmitControl;
use AnhNhan\ModHub\Views\Form\Controls\TextAreaControl;
use AnhNhan\ModHub\Views\Form\Controls\TextControl;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionCreationController extends AbstractForumController
{
    private $defaultLabelValue = "Something pretty descriptive, like 'I want cheezburgrs!'";
    private $defaultTextValue  = "Tell us more about your favourite Pokémon!";

    public function handle()
    {
        $request = $this->request();
        $request->populateFromServer(array("REQUEST_METHOD"));
        $requestMethod = $request->getServerValue("request_method");
        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);

        $violations = null;

        if ($requestMethod == "POST") {
            $request->populateFromRequest(array(
                "label",
                "text",
            ));

            $label = trim($request->getRequestValue("label"));
            $text = trim($request->getRequestValue("text"));
            $validatorInput = array(
                "label" => $label,
                "text"  => $text,
            );
            $violations = $this->validateDiscussion($validatorInput);

            if (!$violations->count()) {
                $app = $this->app();
                $em = $app->getEntityManager();

                $discussion = new Discussion(\AnhNhan\ModHub\Storage\Types\UID::generate("USER"), $label, $text);

                $em->persist($discussion);
                $em->flush();

                $container->push(ModHub\ht("h1", "Successfully inserted discussion '$label'!"));
                $container->push(ModHub\ht("a", "Link", array("href" => "/disq/" . preg_replace("/^(.*?-)/", "", $discussion->uid()))));

                return $payload;
            }
        }

        if ($violations->count()) {
            $container->push(ModHub\ht("h1", "There had been errors!"));
            $container->push(ModHub\ht("pre", print_r($violations, true)));
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
