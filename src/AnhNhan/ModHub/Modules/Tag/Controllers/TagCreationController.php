<?php
namespace AnhNhan\ModHub\Modules\Tag\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Tag\Storage\Tag;
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
        $requestMethod = $request->getMethod();
        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);

        $errors  = array();
        $e_label = null;
        $e_text  = null;

        if ($requestMethod == "POST") {
            $label = trim($request->request->get("label"));
            $color = trim($request->request->get("color"));
            $order = trim($request->request->get("disporder"));
            $descr = trim($request->request->get("description"));

            if (!$color) {
                // Just to be sure :)
                $color = null;
            }

            if (!$errors) {
                $app = $this->app();
                $em = $app->getEntityManager();

                $tag = new Tag($label, $color, $descr, (int) $order);

                $em->persist($tag);
                $em->flush();

                $container->push(ModHub\ht("h1", "Successfully inserted tag '$label'!"));
                $container->push(ModHub\ht("a", "Link", array("href" => "/tag/" . preg_replace("/^(.*?-)/", "", $tag->uid()))));

                return $payload;
            }
        }

        $form = new FormView;
        $form->setId("tag-creation");
        $form
            ->setTitle("Create new tag")
            ->setAction("/tag/create")
            ->setMethod("POST");

        $form->append(id(new TextControl())
            ->setLabel("Label")
            ->setName("label")
            ->setValue(""));

        $form->append(id(new TextControl())
            ->setLabel("Color")
            ->setName("color")
            ->setValue(""));

        $form->append(id(new TextControl())
            ->setLabel("Display order")
            ->setName("disporder")
            ->setValue("0"));

        $form->append(id(new TextAreaControl())
            ->setLabel("Description")
            ->setName("description")
            ->setValue(""));

        $form->append(id(new SubmitControl())
            ->addCancelButton("/tag/")
            ->addSubmitButton("Press the button!"));

        $container->push($form);

        return $payload;
    }
}
