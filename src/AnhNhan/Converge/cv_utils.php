<?php

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
