<?php
namespace AnhNhan\Converge\Modules\Newsroom\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\TemplateMarkupRule;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class QaQuestion extends TemplateMarkupRule
{
    public function get_key()
    {
        return 'qa-question(-block|-paragraph|-h(\d))?';
    }

    public function apply_occurence($matches)
    {
        $type = $matches[1];
        $fun = $type == '-block'
            ? function ($x) { return div('', $x); }
            : ($type == '-paragraph'
                ? function ($x) { return cv\ht('p', $x); }
                : ($type
                    ? ltrim($type, '-')
                    : function ($x) { return span('', $x); }));
        return $fun(cv\safeHtml(trim($matches[3])))->addClass('qa-question');
    }
}
