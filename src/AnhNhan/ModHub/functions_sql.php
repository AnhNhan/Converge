<?php

const SearchPrepStmt_Include = 'inc';
const SearchPrepStmt_Exclude = 'exc';

const SearchType_All         = 'all';
const SearchType_Any         = 'any';

const InputMode_Uid          = 'uid';
const InputMode_Name         = 'name';

/**
 * Generate the set membership and non-memberships as a chunk of the
 * prepared statement SQL query.
 *
 * @param  array  $set An array of [ $whatever => 'inc' / 'exc' (use the constants) ]
 *
 * @return string The generated query.
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
function generate_search_prep_stmt_part(array $set, $name, $search_type = SearchType_All)
{
    $query = '';
    $joiner_pred = $search_type == SearchType_All ? ' AND ' : ' OR ';

    $counts = [SearchPrepStmt_Include => 0, SearchPrepStmt_Exclude => 0];
    foreach ($set as $v) {
        $counts[$v]++;
    }

    $iiii = 0;
    $incs = [];
    $excs = [];

    for ($_ = 0; $_ < $counts[SearchPrepStmt_Include]; $_++)
    {
        $incs[] = $name . ' = ?' . $iiii;
        $iiii++;
    }
    for ($_ = 0; $_ < $counts[SearchPrepStmt_Exclude]; $_++)
    {
        $excs[] = $name . ' = ?' . $iiii;
        $iiii++;
    }

    if ($incs)
    {
        $query = '(' . implode($joiner_pred, $incs) . ')';
    }
    if ($excs)
    {
        if ($incs)
        {
            $query .= ' AND NOT ';
        }
        $query .= '(' . implode($joiner_pred, $excs) . ')';
    }

    return $query;
}
