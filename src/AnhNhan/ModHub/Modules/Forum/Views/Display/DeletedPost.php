<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Display;

use AnhNhan\ModHub as mh;
use AnhNhan\ModHub\Views\Panel\Panel;
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
        $title->push(mh\ht('div', $this->date)->addClass('pull-right'));
        $title->push(div('', h3(mh\icon_ion('Deleted Post', 'close', false))));
        $postPanel->setHeader($title);

        return $postPanel;
    }
}
