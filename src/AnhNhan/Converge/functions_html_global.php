<?php

// No namespace for global functions

use AnhNhan\Converge as cv;

function div($class = "", $contents = null, $id = null)
{
    return cv\ht('div', $contents)
        ->addClass($class)
        ->setId($id)
    ;
}

function span($class, $contents)
{
    return cv\ht('span', $contents)
        ->addClass($class)
    ;
}

function a($contents = null, $href = null, $backbone = false)
{
    return cv\ht('a', $contents, array("backbone" => $backbone, "href" => $href));
}

function h1($contents, $class = '')
{
    return cv\ht('h1', $contents)
        ->addClass($class)
    ;
}

function h2($contents, $class = '')
{
    return cv\ht('h2', $contents)
        ->addClass($class)
    ;
}

function h3($contents, $class = '')
{
    return cv\ht('h3', $contents)
        ->addClass($class)
    ;
}

function h4($contents, $class = '')
{
    return cv\ht('h4', $contents)
        ->addClass($class)
    ;
}

function h5($contents, $class = '')
{
    return cv\ht('h5', $contents)
        ->addClass($class)
    ;
}

function h6($contents, $class = '')
{
    return cv\ht('h6', $contents)
        ->addClass($class)
    ;
}
