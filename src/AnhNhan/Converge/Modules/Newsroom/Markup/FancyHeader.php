<?php
namespace AnhNhan\Converge\Modules\Newsroom\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\TemplateMarkupRule;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class FancyHeader extends TemplateMarkupRule
{
    public function get_key()
    {
        return sprintf('(%s)-(h\d)', implode('|', [
            'cool-header',
            'elegantshadow',
            'deepshadow',
            'insetshadow',
            'retroshadow',
        ]));
    }

    public function apply_occurence($matches)
    {
        return $matches[2](cv\safeHtml($matches[3]), trim($matches[1]));
    }
}
