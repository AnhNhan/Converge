<?php
namespace AnhNhan\ModHub\Modules\Tag\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Tag\Views\TagView;
use AnhNhan\ModHub\Views\Form\FormView;
use AnhNhan\ModHub\Views\Form\Controls\SubmitControl;
use AnhNhan\ModHub\Views\Form\Controls\TextAreaControl;
use AnhNhan\ModHub\Views\Form\Controls\TextControl;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TagCreationController extends AbstractTagController
{
    public function handle()
    {
        $request = $this->request();
        $request->populateFromServer(array("REQUEST_METHOD"));
        $requestMethod = $request->getServerValue("request_method");
        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);

        $errors  = array();
        $e_label = null;
        $e_text  = null;

        if ($requestMethod == "POST") {
            $request->populateFromRequest(array(
                "label",
                "color",
            ));

            $label = trim($request->getRequestValue("label"));
            $color = trim($request->getRequestValue("color"));

            if (!$color) {
                // Just to be sure :)
                $color = null;
            }

            if (!$errors) {
                $app = $this->app();
                $em = $app->getEntityManager();

                $tag = new Tag($label, $color);

                $em->persist($tag);
                $em->flush();

                $container->push(ModHub\ht("h1", "Successfully inserted tag '$label'!"));
                $container->push(ModHub\ht("a", "Link", array("href" => "/tag/" . preg_replace("/^(.*?-)/", "", $tag->uid()))));

                return $payload;
            }
        }

        $form = new FormView;
        $form
            ->setTitle("Create new tag")
            ->setAction("/disq/create")
            ->setMethod("POST");

        $form->append(id(new TextControl())
            ->setLabel("Label")
            ->setName("label")
            ->setValue(""));

        $form->append(id(new TextAreaControl())
            ->setLabel("Color")
            ->setName("color")
            ->setValue(""));

        $form->append(id(new SubmitControl())
            ->addCancelButton("/tag/")
            ->addSubmitButton("Press the button!"));

        $container->push($form);

        return $payload;
    }
}
