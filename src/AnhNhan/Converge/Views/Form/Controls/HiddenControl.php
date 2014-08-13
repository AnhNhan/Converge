<?php
namespace AnhNhan\Converge\Views\Form\Controls;

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

    public function getClasses()
    {
        // Overwrite so we don't get any styles for hidden inputs
        return null;
    }

    public function getOptions()
    {
        $parent = parent::getOptions();
        unset($parent["class"]);
        return $parent;
    }
}
