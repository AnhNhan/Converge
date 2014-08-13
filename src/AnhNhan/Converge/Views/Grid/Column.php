<?php
namespace AnhNhan\Converge\Views\Grid;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\AbstractView;
use YamwLibs\Libs\Html\Interfaces\YamwMarkupInterface;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Column extends AbstractView
{
    private $elements;
    private $size;
    private $parentRow;

    public function __construct($size, MarkupContainer $elements = null, Row $row = null)
    {
        parent::__construct();
        $this->size = $size;
        $this->parentRow = $row;
        $this->elements = $elements ?: new MarkupContainer;
    }

    public function column($size, MarkupContainer $elements = null)
    {
        $column = new Column($size, $elements, $this->parentRow);
        $this->elements->push($column);
        return $column;
    }

    public function parentRow()
    {
        return $this->parentRow;
    }

    public function push(YamwMarkupInterface $markup)
    {
        $this->elements->push($markup);
        return $this;
    }

    public function render()
    {
        $columnTag = Converge\ht("div", $this->elements);
        $columnTag->addClass(sprintf("width%d", $this->size));
        return $columnTag;
    }
}
