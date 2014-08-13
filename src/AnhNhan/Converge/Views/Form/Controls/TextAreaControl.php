<?php
namespace AnhNhan\Converge\Views\Form\Controls;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class TextAreaControl extends AbstractFormControl
{
    /**
     * No effect
     */
    protected function getType()
    {
        return null;
    }

    public function __construct()
    {
        parent::__construct();
        $this->addClass('form-control-textarea');
        $this->setTagName('textarea');
        $this->setValue('');
    }

    public function setValue($value)
    {
        return $this->setContent($value);
    }
}
