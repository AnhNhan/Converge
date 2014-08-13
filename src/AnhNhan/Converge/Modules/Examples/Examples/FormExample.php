<?php
namespace AnhNhan\Converge\Modules\Examples\Examples;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\Form\FormView;
use AnhNhan\Converge\Views\Form\Controls\SubmitControl;
use AnhNhan\Converge\Views\Form\Controls\TextControl;
use AnhNhan\Converge\Views\Form\Controls\TextAreaControl;
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
        $container = Converge\ht("div")->addClass("row-flex");

        $form1 = $this->getForm1();
        $container->append($form1);

        $form2 = $this->getForm2();
        $container->append($form2);

        $form3 = $this->getForm3();
        $container->append($form3);

        return $container;
    }

    public function getForm1()
    {
        $form = new FormView();
        $form->setTitle("Dual column (default)");
        $this->buildForm($form);
        return Converge\ht("div", $form->render())->addClass("width12");
    }

    public function getForm2()
    {
        $form = new FormView();
        $form->setDualColumnMode(false);
        $form->setTitle("Single column");
        $this->buildForm($form);
        return Converge\ht("div", $form->render())->addClass("width12");
    }

    public function getForm3()
    {
        // Load example-specific JS
        $this->getResMgr()->requireJS("application-example-form-tag-selector");

        $form = new FormView;
        $form->setDualColumnMode(false);
        $form->setTitle("TagSelector");

        $form->append(id(new \AnhNhan\Converge\Modules\Tag\Views\FormControls\TagSelector)
            ->setId("form-tag-selector")
            ->setLabel("Tag selection"));

        $form->append(id(new SubmitControl)
            ->addCancelButton('/')
            ->addSubmitButton('Hasta la vista!'));

        return Converge\ht("div", $form->render())->addClass("width12");
    }

    private function buildForm(FormView $form)
    {
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
    }
}
