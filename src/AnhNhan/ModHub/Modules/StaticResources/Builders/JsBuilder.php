<?php
namespace AnhNhan\ModHub\Modules\StaticResources\Builders;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class JsBuilder
{
    public static function buildFile($path)
    {
        // Draft
        return file_get_contents($path);
    }

    public static function buildString($string)
    {
        // Draft
        return $string;
    }
}
