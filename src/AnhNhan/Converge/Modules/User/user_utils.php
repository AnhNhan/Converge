<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\User\Storage\User;

const UserLinkExtra_None      = 'user.extra.none';
const UserLinkExtra_Tooltip   = 'user.extra.tooltip';
const UserLinkExtra_Hovercard = 'user.extra.hovercard';

/**
 * Generates a link to a user. Supports tooltips.
 * May support hovercards in the future.
 */
function link_user(User $user, $full_name = true, $extra = UserLinkExtra_Tooltip)
{
    $display_name =  $full_name ? $user->name : $user->handle;
    $tooltip_name = !$full_name ? $user->name : $user->handle;
    $link = a($display_name, urisprintf('u/%s', $user->canonical_name));
    if ($extra == UserLinkExtra_Tooltip)
    {
        $link
            ->addOption("data-toggle", "tooltip")
            ->addOption("title", $tooltip_name)
        ;
    }
    return $link;
}
