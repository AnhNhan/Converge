<?php
namespace AnhNhan\ModHub;

/**
 * Gets the absolute root path to where this application resides
 *
 * @return string
 * The absolute path to the application root
 */
function get_root()
{
    static $root;
    if (!$root) {
        $root = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
    }
    return $root;
}

/**
 * Gets the absolute path to the folder where `src/` resides in
 *
 * @return string
 * The absolute path to the application root root
 */
function get_root_super()
{
    static $superRoot;
    if (!$superRoot) {
        $superRoot = dirname(get_root()) . DIRECTORY_SEPARATOR;
    }
    return $superRoot;
}

/**
 * Generates the path pointing to the specified resource
 *
 * @param string $val
 * The path from the application root to the resource
 *
 * @param bool $prefix_base_dir
 * If true, it will generate an absolute path, otherwise it will generate a
 * relative one
 *
 * @return string
 * <p>The path to the resource</p>
 */
function path($val = '', $prefix_base_dir = true)
{
    if ($prefix_base_dir) {
        return str_replace(
            array('//', '/', '\\', '\\\\'),
            array(
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR
            ),
            get_root().DIRECTORY_SEPARATOR.$val
        );
    } else {
        return str_replace(
            array('//', '/', '\\', '\\\\'),
            array(
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR,
            ),
            $val
        );
    }
}

/**
 * Whether the current process is a CLI interface or not
 *
 * @return boolean
 * True if PHP is running in CLI mode. Else False.
 */
function is_cli()
{
    return php_sapi_name() == 'cli';
}

function is_windows()
{
    // This is the easiest way to check for Windows - and the fastest :D
    return DIRECTORY_SEPARATOR === '\\';
}

function println($line = '')
{
    echo $line . PHP_EOL;
}

/**
 * Similar to `idx()`, but with `array_shift()`
 *
 * @param array $array
 * @param mixed $default
 *
 * @return mixed The shifted element of the array, or `$default`
 */
function sdx(array &$array, $default = null)
{
    $return = array_shift($array);
    return $return ?: $default;
}

/**
 * Similar to `pdx()`, but with `array_pop()`
 *
 * @param array $array
 * @param mixed $default
 *
 * @return mixed The element that got popped off the end, or `$default`
 */
function pdx(array &$array, $default = null)
{
    $return = array_pop($array);
    return $return ?: $default;
}

