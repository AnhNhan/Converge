<?php

function to_canonical($name)
{
    return phutil_utf8_strtolower(ascii_non_w_replace($name));
}

function to_slug($name)
{
    return phutil_utf8_strtolower(ascii_non_w_replace($name, '-'));
}

/// UTF-8 aware transformation into canonical strings
function ascii_non_w_replace($str, $replace_with = '', $combine_replaced = true)
{
    // Also replace underscores explicitly, they usually are considered
    // towards the alphanumerical characters.
    // We disallow any multibyte characters - we may exempt certain character
    // ranges like CJK and arabian characters later on by white-listing them.
    $_str = phutil_utf8v($str);
    $callback = function ($x) use ($replace_with)
    {
        return (strlen($x) == 1 && !preg_match('/[\\W_]/', $x))
            ? $x
            : $replace_with
        ;
    };
    $replaced = implode('', array_map($callback, $_str));
    if ($replace_with && $combine_replaced)
    {
        $replaced = preg_replace('/(' . preg_quote($replace_with) . ')+/', $replace_with, $replaced);
    }
    return $replaced;
}

function group($list, callable $predicate)
{
    $map = pull($list, $predicate);

    $groups = [];
    // Pre-allocate groups
    foreach ($map as $group)
    {
        $groups[$group] = [];
    }

    foreach ($map as $key => $group) {
        $groups[$group][$key] = $list[$key];
    }

    return $groups;
}

function pull($list, callable $value_predicate)
{
    $result = [];
    foreach ($list as $key => $value)
    {
        $result[$key] = $value_predicate($value);
    }
    return $result;
}

function all($list, callable $bool_predicate = null)
{
    if (!$bool_predicate)
    {
        $bool_predicate = 'id';
    }

    foreach ($list as $key => $value)
    {
        if (!$bool_predicate($value, $key))
        {
            return false;
        }
    }

    return true;
}

function any($list, callable $bool_predicate = null)
{
    if (!$bool_predicate)
    {
        $bool_predicate = 'id';
    }

    foreach ($list as $key => $value)
    {
        if ($bool_predicate($value, $key))
        {
            return true;
        }
    }

    return false;
}

function mkey(array $array, $key)
{
    return mpull($array, null, $key);
}

function pkey(array $array, $key)
{
    return ppull($array, null, $key);
}

function ikey(array $array, $key)
{
    return ipull($array, null, $key);
}

function curry_fa(callable $fun, $first_arg)
{
    return function () use ($fun, $first_arg)
    {
        return call_user_func_array(
            $fun,
            array_merge([$first_arg], func_get_args())
        );
    };
}
