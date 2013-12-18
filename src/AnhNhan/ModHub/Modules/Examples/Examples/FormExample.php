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
        $container = ModHub\ht("div")->addClass("row-flex");

        $form1 = $this->getForm1();
        $container->appendContent(ModHub\ht("h1", "Dual column (default)"));
        $container->appendContent($form1);

        $form2 = $this->getForm2();
        $container->appendContent(ModHub\ht("h1", "Single column"));
        $container->appendContent($form2);

        return $container;
    }

    public function getForm1()
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
        return ModHub\ht("div", $form->render())->addClass("width12");
    }

    public function getForm2()
    {
        $form = new FormView();
        $form->setDualColumnMode(false);
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
        return ModHub\ht("div", $form->render())->addClass("width12");
    }
}
