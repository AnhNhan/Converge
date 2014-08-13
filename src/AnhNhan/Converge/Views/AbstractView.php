<?php
namespace AnhNhan\Converge\Views;

use AnhNhan\Converge\Modules\StaticResources\ResMgr;
use YamwLibs\Libs\Html\Interfaces\YamwMarkupInterface;
use YamwLibs\Libs\Html\Markup\MarkupContainer;
use YamwLibs\Libs\Html\Markup\TextNode;
use YamwLibs\Libs\Html\Markup\SafeTextNode;
use YamwLibs\Libs\View\ViewInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class AbstractView implements ViewInterface, YamwMarkupInterface
{
    /**
     * @var MarkupContainer
     */
    private $objects;

    private $classes = array();
    private $options = array();
    private $id;

    public function __construct()
    {
        $this->objects = new MarkupContainer();
    }

    public function addClass($class)
    {
        $this->classes[] = $class;
        return $this;
    }

    public function addOption($key, $value = null)
    {
        $this->options[] = array($key => $value);
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Appends a child to this view
     *
     * @param mixed $child
     *
     * @return \AnhNhan\Converge\Views\AbstractView
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
     * @return \AnhNhan\Converge\Libs\Html\Markup\HtmlTag
     * An HtmlTag representing this view
     */
    abstract public function render();

    private function process()
    {
        $this->fetchRequiredResources();
        $tag = $this->render();
        if (!$tag || $tag instanceof TextNode) { // May be empty
            return $tag;
        }

        if ($this->id) {
            $tag->setId($this->id);
        }
        $tag->addClass(implode(" ", $this->classes));
        foreach ($this->options as $opt) {
            $tag->addOption(key($opt), current($opt));
        }
        return $tag;
    }

    /**
     * Public interface for $this->process().
     */
    public function getProcessed()
    {
        return $this->process();
    }

    /**
     * Returns the string representation of this view
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $viewObject = $this->process();
            return (string)$viewObject;
        } catch (\Exception $exc) {
            echo $exc->getMessage() . "\n";
            echo $exc->getTraceAsString();
            return "<Invalid String>";
        }
    }

    /*
     * @var ResMgr
     */
    private $resMgr;

    public function setResMgr(ResMgr $resMgr)
    {
        $this->resMgr = $resMgr;
        return $this;
    }

    public function getResMgr()
    {
        if (!$this->resMgr) {
            throw new \RunTimeException(
                sprintf(
                    "Tried to access non-existing ResMgr service from class '%s'!",
                    get_class($this)
                )
            );
        }
        return $this->resMgr;
    }

    private function fetchRequiredResources()
    {
        $resources = $this->getRequiredResources();

        $assertSame = function ($expected, $actual) {
            if ($actual !== $expected) {
                throw new \RunTimeException("Invalid response from 'getRequiredResources'!");
            }
        };

        $assertSame(2, count($resources));
        $assertSame(true, isset($resources["css"]));
        $assertSame(true, isset($resources["js"]));

        $css = $resources["css"];
        if ($css) {
            $resMgr = $this->getResMgr();
            foreach ($css as $_) {
                $resMgr->requireCSS($_);
            }
        }

        $js = $resources["js"];
        if ($js) {
            $resMgr = $this->getResMgr();
            foreach ($js as $_) {
                $resMgr->requireCSS($_);
            }
        }
    }

    /**
     * Returns the static resources required by this view.
     *
     * @return array
     */
    public function getRequiredResources()
    {
        return array(
            "css" => array(),
            "js"  => array(),
        );
    }
}
