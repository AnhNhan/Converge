<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Display;

use AnhNhan\Converge as cv;

use Diff as DiffEngine;
use AnhNhan\Converge\Modules\Markup\Diff\Renderer\Inline as InlineDiffRenderer;

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
        return cv\safeHtml($diff->render(new InlineDiffRenderer));
    }
}
