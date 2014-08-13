<?php
namespace AnhNhan\Converge\Views\Objects;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\AbstractView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Listing extends AbstractView
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
        $container = Converge\ht('div')
            ->addClass('objects-list-container');

        if ($this->title) {
            $container->appendContent(
                Converge\ht('h2', $this->title)
                    ->addClass('objects-list-title')
            );
        }

        $objects = Converge\ht('div')->addClass('objects-list-objects');
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
