<?php

use AnhNhan\Converge\Modules\People\Query\PeopleQuery;

function create_user_query($app_or_em)
{
    return new PeopleQuery($app_or_em);
}

function fetch_external_authors(array $stuff, PeopleQuery $query, $id_field = 'authorId', $set_method = 'setAuthor', $author_field = 'author')
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
    $user_ids = array_unique($user_ids);
    $users = $query->retrieveUsersForUIDs($user_ids);
    foreach ($stuff as $thing)
    {
        $thing->$set_method(idx($users, $thing->$id_field));
    }
}
