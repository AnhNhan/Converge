<?php

function to_canonical($name)
{
    // Also replace underscores explicitly, they usually are considered
    // towards the alphanumerical characters.
    return strtolower(preg_replace('/[\\W_]/', '', $name));
}
