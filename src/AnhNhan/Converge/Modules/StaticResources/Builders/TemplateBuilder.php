<?php
namespace AnhNhan\Converge\Modules\StaticResources\Builders;

/**
 * Actually just copy-pasting
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TemplateBuilder
{
    public static function buildFile($path)
    {
        return file_get_contents($path);
    }

    public static function buildString($string)
    {
        return $string;
    }
}
