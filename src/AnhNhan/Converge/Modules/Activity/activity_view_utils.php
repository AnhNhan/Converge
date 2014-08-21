<?php

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Application\ApplicationList;

function render_activity_listing(array $activities, array $renderers, $title = null, $empty_message = 'No activities')
{
    $container = div('activity-listing');
    if ($title)
    {
        $container->append(h2($title));
    }

    if (!$activities)
    {
        $container->append(div('objects-list-empty-message', 'No tasks available'));
    }
    else
    {
        $big_div = div('columned-activity-listing');
        $container->append($big_div);
        foreach ($activities as $activity)
        {
            $object_type = uid_get_type($activity->object_uid);
            $xact_type   = $activity->xact_type;
            $renderer = idx($renderers, $object_type);
            $header_text =  $renderer ? 'header' : sprintf('Unknown activity: %s -> %s', $object_type, $xact_type);
            if ($activity->object_link)
            {
                $header_text = a($header_text, $activity->object_link);
            }
            $header = cv\hsprintf(
                '<div class="pull-right activity-date">%s</div><img src="%s" class="user-profile-image" /><div><strong>%s</strong> <span class="minor-stuff">%s</span></div>',
                $activity->createdAt->format("D, d M 'y"),
                $activity->actor_object->getGravatarImagePath,
                link_user($activity->actor_object),
                $header_text
            );
            $panel = panel($header, 'activity-panel');
            $big_div->append($panel);
        }
    }

    return $container;
}

function get_activity_renderers($app_list = null)
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

    $rules = mpull($apps, 'getActivityRenderers');
    $rules = array_mergev($rules);
    return $rules;
}
