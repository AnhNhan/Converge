<?php
namespace AnhNhan\Converge\Modules\Activity\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ActivityListing extends ActivityController
{
    public function handle()
    {
        $request = $this->request;
        $query = $this->buildQuery();

        $activities = $query->retrieveActivities(200);

        $activity_renderers = get_activity_renderers($this->app->getService('app.list'));

        $external_uids = activity_get_external_uids($activities, $activity_renderers);
        $external_uids = array_merge($external_uids, ppull($activities, 'actor_uid'));
        $grouped_external_uids = group($external_uids, 'uid_get_type');

        $external_user_uids = idx($grouped_external_uids, 'USER', []);

        $user_query = create_user_query($this->externalApp('people'));
        $external_user_objects = $user_query->retrieveUsersForUIDs($external_user_uids);
        map(function ($activity) use ($external_user_objects) { $activity->actor_object = idx($external_user_objects, $activity->actor_uid); }, $activities);
        $custom_rules = get_custom_markup_rules($this->app->getService('app.list'));

        $other = [
            'markup_rules' => $custom_rules,
            'users'        => $external_user_objects,
        ];

        $container = new MarkupContainer;
        $container->push(h1('Activity Listing'));

        $listing = render_activity_listing($activities, $activity_renderers, $other)->addClass('feed-activity-listing');
        $container->push($listing);

        $payload = $this->payload_html();
        $payload->setTitle('Activity Listing');
        $payload->setPayloadContents($container);
        $payload->resMgr
            ->requireCss('application-activity-listing')
        ;
        return $payload;
    }
}
