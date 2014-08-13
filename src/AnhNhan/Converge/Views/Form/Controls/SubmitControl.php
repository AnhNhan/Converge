<?php
namespace AnhNhan\Converge\Views\Form\Controls;

use AnhNhan\Converge;
use YamwLibs\Libs\View\ViewInterface;
use YamwLibs\Libs\Html\HtmlFactory as HF;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class SubmitControl implements ViewInterface
{
    private $submit_label;
    private $cancel_label;
    private $cancel_uri;

    public function addCancelButton($uri, $label = 'Cancel')
    {
        $this->cancel_uri = $uri;
        $this->cancel_label = $label;
        return $this;
    }

    public function addSubmitButton($label = 'Submit')
    {
        $this->submit_label = $label;
        return $this;
    }

    public function render()
    {
        $container = HF::divTag()
            ->addClass('form-control-container')
            ->addClass('form-control-submit');

        $cancelButton = null;
        if ($this->cancel_uri) {
            $cancelButton = Converge\ht(
                'a',
                Converge\icon_ion($this->cancel_label, 'close', false)
            )->addOption('href', $this->cancel_uri)
                ->addClass('btn btn-default');
        }
        $container->appendContent($cancelButton);

        $submitButton = null;
        if ($this->submit_label) {
            $submitButton = Converge\ht(
                'button',
                Converge\icon_ion($this->submit_label, 'checkmark')
            )->addOption('name', '__submit__')
                ->addClass('btn btn-primary');
        }
        $container->appendContent($submitButton);

        return $container;
    }

    public function __toString()
    {
        return (string)$this->render();
    }
}
