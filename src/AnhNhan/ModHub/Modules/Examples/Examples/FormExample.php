<?php
namespace AnhNhan\ModHub\Modules\Examples\Examples;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\Form\FormView;
use AnhNhan\ModHub\Views\Form\Controls\SubmitControl;
use AnhNhan\ModHub\Views\Form\Controls\TextControl;
use AnhNhan\ModHub\Views\Form\Controls\TextAreaControl;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class FormExample extends AbstractExample
{
    public function getName()
    {
        return "form";
    }

    public function getExample()
    {
        $form = new FormView();
        $form->enableFileUpload()
            ->setAction("user/login");
        $form->append(id(new TextControl())
            ->setLabel('Reason for your coming')
            ->setValue('Peace'));

        $textControl = new TextControl();
        $textControl->setName('username')
            ->setValue('Your name')
            ->setLabel('This is the label for your name');
        $form->append($textControl);

        $form->append(id(new TextControl())
            ->setLabel('Name of your mom')
            ->setValue('Dorothy'));
        $form->append(id(new TextAreaControl())
            ->setValue("This is some text")
            ->setLabel("Description"));
        $form->append(id(new SubmitControl())
            ->addCancelButton('/')
            ->addSubmitButton('Hasta la vista!'));
        return ModHub\ht("div", $form->render()->addClass("width12"))->addClass("row-flex");
    }
}