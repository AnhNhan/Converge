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
