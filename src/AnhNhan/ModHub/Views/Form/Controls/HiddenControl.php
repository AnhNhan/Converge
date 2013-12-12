<?php
namespace AnhNhan\ModHub\Views\Form\Controls;

use YamwLibs\Libs\Html\Markup\HtmlTag;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class HiddenControl extends AbstractFormControl
{
    protected function getType()
    {
        return 'hidden';
    }

    public function render()
    {
        $thisTag = new HtmlTag(
            $this->getTagName(),
            $this->getContent(),
            $this->getOptions()
        );
        $classes = $this->getClasses();
        if ($classes) {
            $thisTag->addClass($this->getClasses());
        }
        $id = $this->getId();
        if ($id) {
            $thisTag->setId($this->getId());
        }
        return $thisTag;
    }
}
