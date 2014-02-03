<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Display;

use AnhNhan\ModHub as mh;

use Diff as DiffEngine;
use Diff_Renderer_Html_Inline as InlineDiffRenderer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class TextChangeLabel extends TextChangeAction
{
    protected function getSubject()
    {
        return "the label";
    }

    protected function renderBody()
    {
        $options = array(
        );
        $diff = new DiffEngine(array($this->prevText), array($this->nextText), $options);
        return mh\safeHtml($diff->render(new InlineDiffRenderer));
    }
}
