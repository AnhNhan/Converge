<?php
namespace AnhNhan\Converge\Modules\Newsroom\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\TemplateMarkupRule;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class LeadCharSepParagraph extends TemplateMarkupRule
{
    public function get_key()
    {
        return 'lead-char-sep-paragraph';
    }

    public function apply_occurence($matches)
    {
        return cv\ht('p', cv\safeHtml(trim($matches[1])))->addClass('lead-char separator-paragraph');
    }
}
