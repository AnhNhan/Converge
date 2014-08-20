<?php
namespace AnhNhan\Converge\Modules\Markup\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupRule;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class AsianText extends MarkupRule
{
    // Users may use dashes, underscores and periods regardless what we tell
    // them, so match them anyway.
    const Regex = '/[{]{2}\s*asian\s*=\s*(.*?)\s*[}]{2}/is';

    public function apply($text)
    {
        return preg_replace_callback(
            self::Regex,
            [$this, 'applyAsian'],
            $text
        );
    }

    public function applyAsian($matches)
    {
        return span('asian-text', trim($matches[1]))
            ->addOption('style', 'font-size: 24px;')
        ;
    }
}
