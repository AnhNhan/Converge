<?php

function to_canonical($name)
{
    // Also replace underscores explicitly, they usually are considered
    // towards the alphanumerical characters.
    return strtolower(preg_replace('/[\\W_]/', '', $name));
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
