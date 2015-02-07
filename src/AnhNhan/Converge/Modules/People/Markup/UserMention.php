<?php
namespace AnhNhan\Converge\Modules\People\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\People\PeopleApplication;
use AnhNhan\Converge\Modules\People\Storage\User;
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

    public function __construct(PeopleApplication $app)
    {
        $this->query = create_user_query($app);
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
        $original = $matches[0];
        $username = to_canonical($original);
        $metadata = [
            'original' => $original,
            'username' => $username,
        ];
        $token = $this->storage->store($original);
        $this->storage->addTokenToSet('user-mention', $token, $metadata);
        return $token;
    }

    public function didMarkupText()
    {
        $token_set = $this->storage->getTokenSet('user-mention');
        if (!$token_set)
        {
            return;
        }

        $usernames = ipull($token_set, 'username');
        $users     = $this->query->retrieveUsersForCanonicalNames($usernames);
        $users     = mkey($users, 'canonical_name');
        $users     = array_map(function ($user)
            {
                return link_user($user)->addClass('user-mention');
            }, $users);
        foreach ($token_set as $token => $metadata)
        {
            $this->storage->overwrite($token, idx($users, $metadata['username'], tooltip('span', $metadata['original'], 'user not found')->addClass('bad-username')));
        }
    }
}
