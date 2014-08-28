<?php

function to_canonical($name)
{
    return phutil_utf8_strtolower(canonical_replace($name));
}

/// UTF-8 aware transformation into canonical strings
function canonical_replace($str)
{
    // Also replace underscores explicitly, they usually are considered
    // towards the alphanumerical characters.
    // We disallow any multibyte characters - we may exempt certain character
    // ranges like CJK and arabian characters later on by white-listing them.
    $_str = phutil_utf8v($str);
    $callback = function ($x) { return strlen($x) == 1 && !preg_match('/[\\W_]/', $x); };
    return implode('', array_filter($_str, $callback));
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
