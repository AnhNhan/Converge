<?php

function uid_get_type($uid)
{
    return preg_replace('/([\w]{4}(-[\w]{4})?)-[\w]+/i', '$1', $uid);
}
