<?php
namespace AnhNhan\ModHub\Views\Grid;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\AbstractView;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Row extends AbstractView
{
    private $elements;
    private $isFlex = true;

    public function __construct($flex = true)
    {
        parent::__construct();
        $this->isFlex = $flex;
        $this->elements = new MarkupContainer;
    }

    public function column($size = 12, MarkupContainer $contents = null)
    {
        $column = new Column($size, $contents, $this);
        $this->elements->push($column);
        return $column;
    }

    public function row($flex = true)
    {
        $row = new Row($flex);
        $this->elements->push($row);
        return $row;
    }

    public function render()
    {
        $rowTag = ModHub\ht("div", $this->elements);
        $rowTag->addClass($this->isFlex ? "row-flex" : "row");
        return $rowTag;
    }
}
