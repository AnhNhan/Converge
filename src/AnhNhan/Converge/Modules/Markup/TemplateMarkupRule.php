<?php
namespace AnhNhan\Converge\Modules\Markup;

use AnhNhan\Converge as cv;

/**
 * Useful abstraction for markup in the style of MediaWiki's template markup.
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class TemplateMarkupRule extends MarkupRule
{
    private function generate_regex($key, $i = true, $s = true)
    {
        return str_replace('-key-', $key, '/[{]{2}\s*-key-\s*=\s*(.*?)\s*[}]{2}/' . ($i ? 'i' : '') . ($s ? 's' : ''));
    }

    public function apply($text)
    {
        return preg_replace_callback(
            $this->generate_regex($this->get_key()),
            [$this, 'apply_occurence'],
            $text
        );
    }

    abstract public function get_key();
    abstract public function apply_occurence($matches);
}
