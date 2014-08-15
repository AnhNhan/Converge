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

    private $empty_text;

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

    public function setEmptyMessage($empty_text)
    {
        $this->empty_text = $empty_text;
        return $this;
    }

    public function render()
    {
        $container = Converge\ht('div')
            ->addClass('objects-list-container');

        if ($this->title) {
            $container->append(
                Converge\ht('h2', $this->title)
                    ->addClass('objects-list-title')
            );
        }

        if ($this->objects)
        {
            $objects = Converge\ht('div')->addClass('objects-list-objects');
            foreach ($this->objects as $object) {
                $objects->append($object);
            }
            $container->append($objects);
        }
        else
        {
            $empty_box = Converge\ht('div')->addClass('objects-list-empty-message');
            $empty_box->append($this->empty_text ?: 'No objects in this listing');
            $container->append($empty_box);
        }

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
