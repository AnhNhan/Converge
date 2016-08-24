<?php

use AnhNhan\Converge\Application\ApplicationList;

function get_custom_markup_rules($app_list = null)
{
    $apps = [];

    if ($app_list instanceof ApplicationList)
    {
        $apps = $app_list->apps();
    }
    else
    {
        $apps = \AnhNhan\Converge\Modules\Symbols\SymbolLoader::getInstance()
            ->getEnabledApplications()
        ;
    }

    $rules = mpull($apps, 'getCustomMarkupRules');
    $rules = array_mergev($rules);
    return $rules;
}
