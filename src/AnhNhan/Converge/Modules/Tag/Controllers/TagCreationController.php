<?php
namespace AnhNhan\Converge\Modules\Tag\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Modules\Tag\Storage\TagTransaction;
use AnhNhan\Converge\Modules\Tag\Transaction\TagTransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
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
            $descr = Converge\normalize_newlines($descr);

            if (!$color) {
                // Just to be sure :)
                $color = null;
            }

            if (!$errors) {
                $app = $this->app();
                $em = $app->getEntityManager();

                $tag = new Tag;

                $editor = TagTransactionEditor::create($em)
                    ->setEntity($tag)
                    ->setActor($this->user->uid)
                    ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_SKIP)
                    ->addTransaction(
                        TagTransaction::create(TransactionEntity::TYPE_CREATE)
                    )
                    ->addTransaction(
                        TagTransaction::create(TagTransaction::TYPE_EDIT_LABEL, $label)
                    )
                    ->addTransaction(
                        TagTransaction::create(TagTransaction::TYPE_EDIT_COLOR, $color)
                    )
                    ->addTransaction(
                        TagTransaction::create(TagTransaction::TYPE_EDIT_DESC, $descr)
                    )
                    ->addTransaction(
                        TagTransaction::create(TagTransaction::TYPE_EDIT_ORDER, (int) $order)
                    )
                ;
                $editor->apply();

                $targetURI = "/tag/" . $tag->cleanId();
                return new RedirectResponse($targetURI);
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
