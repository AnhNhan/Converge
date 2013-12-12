<?php
namespace AnhNhan\ModHub\Views\Form\Controls;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class TextControl extends AbstractFormControl
{
    public function __construct()
    {
        parent::__construct();
        $this->addClass('form-control-text');
    }

    protected function getType()
    {
        return 'text';
    }
}
