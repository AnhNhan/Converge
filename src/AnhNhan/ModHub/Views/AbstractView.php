<?php
namespace AnhNhan\ModHub\Views;

use YamwLibs\Libs\Html\Interfaces\YamwMarkupInterface;
use YamwLibs\Libs\Html\Markup\MarkupContainer;
use YamwLibs\Libs\Html\Markup\TextNode;
use YamwLibs\Libs\Html\Markup\SafeTextNode;
use YamwLibs\Libs\View\ViewInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class AbstractView implements ViewInterface
{
    /**
     * @var MarkupContainer
     */
    private $objects;

    public function __construct()
    {
        $this->objects = new MarkupContainer();
    }

    /**
     * Appends a child to this view
     *
     * @param mixed $child
     *
     * @return \AnhNhan\ModHub\Views\AbstractView
     */
    public function append($child)
    {
        // ViewInterface objects produce safe HTML
        if (is_object($child) && !($child instanceof YamwMarkupInterface) &&
            $child instanceof ViewInterface) {
            $child = new SafeTextNode($child);
        } elseif (!is_object($child) || !($child instanceof YamwMarkupInterface)) {
            $child = new TextNode($child);
        }
        $this->objects->push($child);

        return $this;
    }

    /**
     * Returns the childs that had beed inserted into this view
     *
     * @return MarkupContainer
     */
    protected function retrieveChilds()
    {
        return $this->objects;
    }

    /**
     * Renders this view into an appropriate form with the current configuration
     *
     * @return \AnhNhan\ModHub\Libs\Html\Markup\HtmlTag
     * An HtmlTag representing this view
     */
    abstract public function render();

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
