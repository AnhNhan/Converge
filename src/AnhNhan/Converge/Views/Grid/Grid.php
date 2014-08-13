<?php
namespace AnhNhan\Converge\Views\Grid;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\AbstractView;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Grid extends AbstractView
{
    private $rows;

    public function __construct()
    {
        parent::__construct();
        $this->rows = new MarkupContainer;
    }

    public function row($flex = true)
    {
        $row = new Row($flex, $this);
        $this->rows->push($row);
        return $row;
    }

    public function render()
    {
        $gridTag = Converge\ht("div", $this->rows)->addClass("grid-system");
        return $gridTag;
    }
}
