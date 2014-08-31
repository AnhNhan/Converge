<?php
namespace AnhNhan\Converge\Modules\Newsroom\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\TemplateMarkupRule;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class QaAnswerer extends TemplateMarkupRule
{
    public function get_key()
    {
        return 'qa-answerer';
    }

    public function apply_occurence($matches)
    {
        return strong(cv\safeHtml(trim($matches[1])))->addClass('qa-answerer');
    }
}
