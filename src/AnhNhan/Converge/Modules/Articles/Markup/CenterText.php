<?php
namespace AnhNhan\Converge\Modules\Articles\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\TemplateMarkupRule;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class CenterText extends TemplateMarkupRule
{
    public function get_key()
    {
        return 'center-(block|text)';
    }

    public function apply_occurence($matches)
    {
        $fun = $matches[1] == 'block' ? 'div' : 'span';
        return $fun('', cv\safeHtml(trim($matches[2])))->addOption('style', 'text-align: center;');
    }
}
