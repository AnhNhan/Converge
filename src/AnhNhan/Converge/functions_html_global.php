<?php

// No namespace for global functions

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Grid\Grid;
use AnhNhan\Converge\Views\Panel\Panel;

use YamwLibs\Libs\Html\Markup\TextNode;

function implode_safeHtml($glue, array $pieces)
{
    $escape_fun = function ($x) { return new TextNode($x); };
    $_pieces = [];
    foreach (array_map($escape_fun, $pieces) as $escaped_piece)
    {
        $_pieces[] = $escaped_piece;
    }
    return cv\safeHtml(implode($escape_fun($glue), $_pieces));
}

function nbsp()
{
    return cv\safeHtml('&nbsp;');
}

function grid()
{
    return new Grid;
}

function panel($header = null, $class = '')
{
    return id(new Panel)
        ->addClass($class)
        ->setHeader($header)
    ;
}

function popover($tag_name, $tag_contents, $popover_contents)
{
    return cv\ht($tag_name, $tag_contents)
        ->addOption("data-toggle", "popover")
        ->addOption("data-content", $popover_contents)
    ;
}

function tooltip($tag_name, $tag_contents, $tooltip)
{
    return cv\ht($tag_name, $tag_contents)
        ->addOption("data-toggle", "tooltip")
        ->addOption("title", $tooltip)
    ;
}

function div($class = "", $contents = null, $id = null)
{
    return cv\ht('div', $contents)
        ->addClass($class)
        ->setId($id)
    ;
}

function span($class, $contents = '')
{
    return cv\ht('span', $contents)
        ->addClass($class)
    ;
}

function strong($contents)
{
    return cv\ht('strong', $contents);
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
