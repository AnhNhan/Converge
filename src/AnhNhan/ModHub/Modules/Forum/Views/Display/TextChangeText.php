<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Display;

use AnhNhan\ModHub as mh;

use Diff as DiffEngine;
use Diff_Renderer_Html_SideBySide as SideBySideDiffRenderer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class TextChangeText extends TextChangeAction
{
    protected function getSubject()
    {
        return "the main text";
    }

    protected function renderBody()
    {
        $options = array(
        );
        $diff = new DiffEngine(explode("\n", $this->prevText), explode("\n", $this->nextText), $options);
        return mh\safeHtml($diff->render(new SideBySideDiffRenderer));
    }
}
