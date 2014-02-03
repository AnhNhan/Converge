<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Display;

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
        return "";
    }
}
