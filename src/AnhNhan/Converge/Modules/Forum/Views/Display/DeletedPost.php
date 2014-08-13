<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Display;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class DeletedPost extends ForumDisplayObject
{
    public function render()
    {
        $postPanel = new Panel;
        //$postPanel->setColor(Panel::COLOR_DARK);

        $title = new MarkupContainer;
        $title->push(cv\ht('div', $this->date)->addClass('pull-right'));
        $title->push(div('', h3(cv\icon_ion('Deleted Post', 'close', false))));
        $postPanel->setHeader($title);

        return $postPanel;
    }
}
