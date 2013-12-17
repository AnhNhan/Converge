<?php
namespace AnhNhan\ModHub\Views\Objects;

use AnhNhan\ModHub;
use YamwLibs\Libs\View\ViewInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Listing implements ViewInterface
{
    private $title;
    private $objects = array();

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function addObject(AbstractObject $object)
    {
        $this->objects[] = $object;
        return $this;
    }

    public function render()
    {
        $container = ModHub\ht('div')
            ->addClass('objects-list-container');

        if ($this->title) {
            $container->appendContent(
                ModHub\ht('h2', $this->title)
                    ->addClass('objects-list-title')
            );
        }

        $objects = ModHub\ht('div')->addClass('objects-list-objects');
        foreach ($this->objects as $object) {
            $objects->appendContent($object);
        }
        $container->appendContent($objects);

        return $container;
    }

    /**
     * Returns the string representation of this view
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->render();
    }
}
