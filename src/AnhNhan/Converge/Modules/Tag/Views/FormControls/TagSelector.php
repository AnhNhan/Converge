<?php
namespace AnhNhan\Converge\Modules\Tag\Views\FormControls;

use AnhNhan\Converge\Views\Form\Controls\AbstractFormControl;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class TagSelector extends AbstractFormControl
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
        $this->addClass('form-control-tag-selector');
        $this->setTagName('textarea');
        $this->addOption('rows', 1);
        $this->setValue('');
    }

    public function setValue($value)
    {
        return $this->setContent($value);
    }
}
