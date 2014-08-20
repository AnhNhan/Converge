<?php
namespace AnhNhan\Converge\Modules\Articles\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\TemplateMarkupRule;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class FontSize extends TemplateMarkupRule
{
    public function get_key()
    {
        return 'font-size-(block|text)-(\d)';
    }

    public function apply_occurence($matches)
    {
        $fun = $matches[1] == 'block' ? 'div' : 'span';
        return $fun('', cv\safeHtml(trim($matches[3])))->addOption('style', sprintf('font-size: %dem;', $matches[2]));
    }
}
