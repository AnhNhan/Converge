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
    // towards word characters.
    // We disallow any multibyte characters - we may exempt certain character
    // ranges like CJK and arabian characters later on by white-listing them.
    // Also characters like â or é don't get normalized, but deleted. We will
    // prefer normalization. Thank you.
    $_str = phutil_utf8v($str);
    $callback = function ($x) use ($replace_with)
    {
        return (strlen($x) == 1 && !preg_match('/[\\W_]/', $x))
            ? $x
            : $replace_with
        ;
    };
    $replaced = implode('', array_map($callback, $_str));
    if (strlen($replace_with) && $combine_replaced)
    {
        $replaced = preg_replace('/(' . preg_quote($replace_with) . ')+/', $replace_with, $replaced);
    }
    return $replaced;
}

function group($list, callable $predicate)
{
    $map = map($predicate, $list);

    $groups = [];
    // Pre-allocate groups
    foreach ($map as $group)
    {
        $groups[$group] = [];
    }

    foreach ($map as $key => $group)
    {
        $groups[$group][$key] = $list[$key];
    }

    return $groups;
}

/// Maps eagerly over any iterable value. Like array_map, but is not restricted to arrays.
function map(callable $value_predicate, $list)
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

function curry_fa(callable $fun, $first_arg /* , ... */)
{
    $first_args = array_slice(func_get_args(), 1);
    return function () use ($fun, $first_args)
    {
        return call_user_func_array(
            $fun,
            array_merge($first_args, func_get_args())
        );
    };
}

function curry_la(callable $fun, $last_arg /* , ... */)
{
    $last_args = array_slice(func_get_args(), 1);
    return function () use ($fun, $last_args)
    {
        return call_user_func_array(
            $fun,
            array_merge(func_get_args(), $last_args)
        );
    };
}

// TODO: Check whether this actually works
/**
 * Memoizes a function, remembering function call results and thus saving
 * repeated invocation of a function.
 *
 * @param Return(Args) $fun
 * @return Return(Args)
 */
function memoize(callable $fun)
{
    $memory = [];
    return function () use ($fun, &$memory)
    {
        $args = func_get_args();
        if (isset($memory[$args]))
        {
            return $memory[$args];
        }
        $result = call_user_func_array($fun, $args);
        $memory[$args] = $result;
        return $result;
    };
}

// Those two may have swapped names, I do not exactly remember which is which

function flatten(callable $fun)
{
    return function ($args) use ($fun)
    {
        return call_user_func_array($fun, $args);
    };
}

function unflatten(callable $fun)
{
    return function () use ($fun)
    {
        return call_user_func_array($fun, func_get_args());
    };
}

function typeDescr($val)
{
    if (is_object($val))
    {
        $descr = sprintf('Object::%s', get_class($val));

        // if (method_exists($val, 'toString'))
        // {
        //     $descr .= sprintf(' {\n    %s\n}', $val);
        // }

        return $descr;
    }
    else
    {
        if (is_array($val))
        {
            $descr = sprintf('Array[%d]', count($val));
            if (count($val) < 15)
            {
                $descr .= sprintf(' {%s}', implode(' + ', array_map('typeDescr', $val)));
            }
            return $descr;
        }
        else if (is_string($val))
        {
            return sprintf('String[%d]', strlen($val));
        }
        else if (is_scalar($val))
        {
            return sprintf('Scalar(%s)', $val);
        }
        else
        {
            throw new \Exception('I forgot a type...');
        }
    }
}

function map_apply_instance_method($method_name, array $params = [])
{
    return function ($obj) use ($method_name, $params)
    {
        if (!is_object($obj))
        {
            throw new \Exception(sprintf("Passed value is not an object! Is of type '%s'", typeDescr($obj)));
        }

        return call_user_func_array([$obj, $method_name], $params);
    };
}

function flat_map(array $array, callable $fun)
{
    return array_mergev(array_map($fun, $array));
}
