<?php
namespace AnhNhan\Converge\Modules\Markup\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\TemplateMarkupRule;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class AsianText extends TemplateMarkupRule
{
    public function get_key()
    {
        return 'asian(|-block)';
    }

    public function apply_occurence($matches)
    {
        $fun = $matches[1] == '-block' ? 'div' : 'span';
        return $fun('asian-text', cv\safeHtml(trim($matches[2])))
            ->addOption('style', 'font-size: 24px;')
        ;
    }
}
