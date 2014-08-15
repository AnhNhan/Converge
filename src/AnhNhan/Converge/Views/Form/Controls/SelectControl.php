<?php
namespace AnhNhan\Converge\Views\Form\Controls;

use AnhNhan\Converge as cv;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class SelectControl extends AbstractFormControl
{
    private $entries = [];
    private $selected;

    public function __construct($size = 1)
    {
        parent::__construct();
        $this->addClass('form-control-select');
        $this->setTagName('select');
        if ($size)
        {
            $this->addOption('size', $size);
        }
    }

    public function addEntry(array $entry)
    {
        $this->entries[] = $entry;
        return $this;
    }

    public function setSelected($selected)
    {
        $this->selected = $selected;
        return $this;
    }

    protected function willRender()
    {
        foreach ($this->entries as $entry)
        {
            $option = cv\ht('option', $entry['label']);

            if ($value = idx($entry, 'value'))
            {
                $option->addOption('value', $value);
            }
            if ($this->selected)
            {
                if ($value == $this->selected)
                {
                    $option->addOption('selected', 'selected');
                }
            }
            $this->append($option);
        }
    }

    protected function getType()
    {
        return 'select';
    }
}
