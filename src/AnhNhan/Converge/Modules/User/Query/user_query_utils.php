<?php

use AnhNhan\Converge\Modules\User\Query\UserQuery;

function create_user_query($app_or_em)
{
    return new UserQuery($app_or_em);
}

function fetch_external_authors(array $stuff, UserQuery $query, $id_field = 'authorId', $set_method = 'setAuthor', $author_field = 'author')
{
    if (empty($stuff))
    {
        return;
    }
    if (count(array_filter(mpull($stuff, $author_field))) == count($stuff))
    {
        return;
    }

    $user_ids = mpull($stuff, $id_field);
    $users = $query->retrieveUsersForUIDs($user_ids);
    foreach ($stuff as $thing)
    {
        $thing->$set_method(idx($users, $thing->$id_field));
    }
}
