<?php
namespace AnhNhan\ModHub\Modules\StaticResources\Builders;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class JsBuilder
{
    const SERVICE_NONE     = "none";
    const SERVICE_UGLIFYJS = "uglifyjs";

    public static $service = self::SERVICE_NONE;

    public static function buildFile($path)
    {
        if (self::$service == self::SERVICE_UGLIFYJS) {
            // Please put the options after the input files
            list($output, $err) = execx('uglifyjs %s -c -m', $path);
            return $output;
        } else {
            return file_get_contents($path);
        }
    }

    public static function buildString($string)
    {
        if (self::$service == self::SERVICE_UGLIFYJS) {
            // Stupid UglifyJs, can't open stdin on Windows...
            $path = \Filesystem::writeUniqueFile(sys_get_temp_dir(), $string);
            $output = self::buildFile($path);

            // Remove tmp file
            \Filesystem::remove($path);
            return $output;
        } else {
            return $string;
        }
    }
}
