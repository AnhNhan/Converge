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
        $apps = \AnhNhan\Converge\Modules\SymbolsSymbolLoader::getInstance()
            ->getObjectsThatDeriveFrom('AnhNhan\Converge\Web\Application\BaseApplication')
        ;
    }

    $rules = mfilter($apps, 'isApplicationEnabled');
    $rules = mpull($apps, 'getCustomMarkupRules');
    $rules = array_mergev($rules);
    return $rules;
}
