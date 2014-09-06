<?php
namespace AnhNhan\Converge\Modules\User\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\User\UserApplication;
use AnhNhan\Converge\Modules\User\Query\UserQuery;
use AnhNhan\Converge\Modules\User\Storage\User;
use AnhNhan\Converge\Modules\Markup\MarkupRule;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class UserMention extends MarkupRule
{
    // Users may use dashes, underscores and periods regardless what we tell
    // them, so match them anyway.
    const Regex = '/(?<!\w|@)@([\w-_.]+[\w])/';

    private $query;

    public function __construct(UserApplication $app)
    {
        $this->query = new UserQuery($app);
    }

    public function apply($text)
    {
        return preg_replace_callback(
            self::Regex,
            [$this, 'applyMention'],
            $text
        );
    }

    public function applyMention($matches)
    {
        $username = to_canonical($matches[0]);
        $user     = head($this->query->retrieveUsersForCanonicalNames([$username]));
        if (!$user)
        {
            return tooltip('span', $matches[0], 'user not found')->addClass('bad-username');
        }

        return link_user($user)->addClass('user-mention');
    }
}
