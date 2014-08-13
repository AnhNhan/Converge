<?php
namespace AnhNhan\Converge\Views\Grid;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\AbstractView;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Row extends AbstractView
{
    private $elements;
    private $isFlex = true;
    private $parent;

    public function __construct($flex = true, $parent = null)
    {
        parent::__construct();
        $this->isFlex = $flex;
        $this->elements = new MarkupContainer;
        $this->parent = $parent;
    }

    public function column($size = 12, MarkupContainer $contents = null)
    {
        $column = new Column($size, $contents, $this);
        $this->elements->push($column);
        return $column;
    }

    public function row($flex = true)
    {
        $row = new Row($flex, $this->parent);
        $this->elements->push($row);
        return $row;
    }

    public function parent()
    {
        return $this->parent;
    }

    public function render()
    {
        $rowTag = Converge\ht("div", $this->elements);
        $rowTag->addClass($this->isFlex ? "row-flex" : "row");
        return $rowTag;
    }
}
