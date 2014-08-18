<?php
namespace AnhNhan\Converge\Views\Property;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\AbstractView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class PropertyList extends AbstractView
{
    private $title;
    private $entries = [];

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function addEntryObj(Entry $entry)
    {
        $this->entries[] = $entry;
        return $this;
    }

    public function addEntry($name, $detail)
    {
        return $this->addEntryObj(new Entry($name, $detail));
    }

    public function render()
    {
        $container = div('property-list');

        if ($this->title)
        {
            $container->append(h2($this->title));
        }

        foreach ($this->entries as $entry)
        {
            $container->append($entry);
        }

        return $container;
    }
}
