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
        return 'asian';
    }

    public function apply_occurence($matches)
    {
        return span('asian-text', trim($matches[1]))
            ->addOption('style', 'font-size: 24px;')
        ;
    }
}
