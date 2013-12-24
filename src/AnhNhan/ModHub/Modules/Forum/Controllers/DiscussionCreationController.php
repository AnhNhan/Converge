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

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionCreationController extends AbstractForumController
{
    public function handle()
    {
        $request = $this->request();
        $request->populateFromServer(array("REQUEST_METHOD"));
        $requestMethod = $request->getServerValue("request_method");
        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);

        // TODO: Replace these through some MOTD-like strings
        $defaultLabelValue = "Something pretty descriptive, like 'I want cheezburgrs!'";
        $defaultTextValue  = "Tell us more about your favourite Pokémon!";

        $errors  = array();
        $e_label = null;
        $e_text  = null;

        if ($requestMethod == "POST") {
            $request->populateFromRequest(array(
                "label",
                "text",
            ));
            $label = trim($request->getRequestValue("label"));
            $text = trim($request->getRequestValue("text"));

            if (empty($label)) {
                $errors[] = "Label is empty";
                $e_label  = "Empty";
            } else if (phutil_utf8_strtolower($defaultLabelValue) == phutil_utf8_strtolower($label)) {
                $errors[] = "Label is still the default one";
                $e_label  = "Please change this";
            }

            if (!$errors) {
                $app = $this->app();
                $em = $app->getEntityManager();

                $discussion = new Discussion($label);
                $post = new Post($discussion, \AnhNhan\ModHub\Storage\Types\UID::generate("USER"), $text);
                $discussion->firstPost($post);

                $em->persist($discussion);
                $em->persist($post);
                $em->flush();

                $container->push(ModHub\ht("h1", "Successfully inserted discussion '$label'!"));
                $container->push(ModHub\ht("a", "Link", array("href" => "/disq/" . preg_replace("/^(.*?-)/", "", $discussion->uid()))));

                return $payload;
            }
        }

        if ($errors) {
            $container->push(ModHub\ht("h1", "There had been errors!"));
            $container->push(ModHub\ht("pre", print_r($errors, true)));
        }

        $form = new FormView;
        $form
            ->setTitle("New discussion")
            ->setAction("/disq/create")
            ->setMethod("POST");

        $form->append(id(new TextControl())
            ->setLabel("Label")
            ->setName("label")
            ->setValue($defaultLabelValue));

        $form->append(id(new TextAreaControl())
            ->setLabel("Text")
            ->setName("text")
            ->setValue($defaultTextValue));

        $form->append(id(new SubmitControl())
            ->addCancelButton("/")
            ->addSubmitButton("Hasta la vista!"));

        $container->push($form);

        return $payload;
    }
}
