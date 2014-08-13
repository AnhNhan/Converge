<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\User\Storage\User;

/**
 * Generates a link to a user. May support hovercards in the future.
 *
 * @param  User   $user      [description]
 * @param  bool   $hovercard [description]
 */
function link_user(User $user, $full_name = true, $hovercard = true)
{
    return a($full_name ? $user->name : $user->canonical_name, urisprintf('u/%s', $user->canonical_name));
}
