<?php

// No namespace for global functions

use AnhNhan\ModHub as mh;

function div($class, $contents = null, $id = null)
{
    return mh\ht('div', $contents)
        ->addClass($class)
        ->setId($id)
    ;
}

function span($class, $contents)
{
    return mh\ht('span', $contents)
        ->addClass($class)
    ;
}

function a($contents = null, $href = null, $backbone = false)
{
    return mh\ht('a', $contents, array("backbone" => $backbone, "href" => $href));
}

function h1($contents, $class = '')
{
    return mh\ht('h1', $contents)
        ->addClass($class)
    ;
}

function h2($contents, $class = '')
{
    return mh\ht('h2', $contents)
        ->addClass($class)
    ;
}

function h3($contents, $class = '')
{
    return mh\ht('h3', $contents)
        ->addClass($class)
    ;
}

function h4($contents, $class = '')
{
    return mh\ht('h4', $contents)
        ->addClass($class)
    ;
}

function h5($contents, $class = '')
{
    return mh\ht('h5', $contents)
        ->addClass($class)
    ;
}

function h6($contents, $class = '')
{
    return mh\ht('h6', $contents)
        ->addClass($class)
    ;
}
