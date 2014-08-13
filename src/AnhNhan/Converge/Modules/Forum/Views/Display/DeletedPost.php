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
        $postPanel = panel()
            ->setHeader(div('', h3(cv\icon_ion('Deleted Post', 'close', false))->append(' ')->append(cv\ht('small', $this->date))))
        ;
        return $postPanel;
    }
}
