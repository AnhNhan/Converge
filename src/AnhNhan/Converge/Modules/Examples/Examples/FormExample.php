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
        return 'form';
    }

    public function getExample()
    {
        $container = div('row-flex');

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
        $form = form('Dual column (default)');
        $this->buildForm($form);
        return div('width12', $form->render());
    }

    public function getForm2()
    {
        $form = form('Single column');
        $form->setDualColumnMode(false);
        $this->buildForm($form);
        return div('width12', $form->render())->addClass('width12');
    }

    public function getForm3()
    {
        // Load example-specific JS
        $this->getResMgr()->requireJS('application-example-form-tag-selector');

        $form = form('TagSelector');
        $form->setDualColumnMode(false);

        $form->append((new \AnhNhan\Converge\Modules\Tag\Views\FormControls\TagSelector)
            ->setId('form-tag-selector')
            ->setLabel('Tag selection'));

        $form->append(form_submitcontrol('/', 'Hasta la vista!'));

        return div('width12', $form->render());
    }

    private function buildForm(FormView $form)
    {
        $form->append(form_textcontrol('Reason for your coming', null, 'Peace'));
        $form->append(form_textcontrol('Your name', 'username', 'Your name')
            ->setHelp('Put your name here'));
        $form->append(form_textcontrol('Name of your mom', null, 'Dorothy')->setError('That ain\' your mother!'));
        $form->append(form_textareacontrol('Description', 'This is some text'));
        $form->append(form_submitcontrol('/', 'Hasta la vista!'));
    }
}
