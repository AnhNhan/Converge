<?php

function curry_fa(callable $fun, $first_arg)
{
    return function () use ($fun, $first_arg) { return call_user_func_array($fun, array_merge([$first_arg], func_get_args())); };
}
