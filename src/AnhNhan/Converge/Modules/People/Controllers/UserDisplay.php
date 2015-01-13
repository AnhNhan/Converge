<?php
namespace AnhNhan\Converge\Modules\People\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Activity\Query\ActivityQuery;
use AnhNhan\Converge\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumListing;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
use AnhNhan\Converge\Modules\Task\Query\TaskQuery;
use AnhNhan\Converge\Modules\People\Query\PeopleQuery;
use AnhNhan\Converge\Modules\People\Storage\User;
use AnhNhan\Converge\Views\Grid\Grid;
use AnhNhan\Converge\Views\Panel\Panel;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class UserDisplay extends AbstractPeopleController
{
    public function handle()
    {
        $request = $this->request;

        $req_canon_name = $request->get('name');

        $query = new PeopleQuery($this->app);
        $user  = head($query->retrieveUsersForCanonicalNames([$req_canon_name], 1));

        if (!$user)
        {
            // Q: Put suggestions here?
            return id(new ResponseHtml404)->setText('Could not find a user with the name \'' . $req_canon_name . '\'.');
        }

        $payload = new HtmlPayload;
        $payload->setTitle(sprintf('User \'%s\'', $user->name));
        $container = new MarkupContainer;
        $payload->setPayloadContents($container);

        $container->push(h1($user->name)->append(' ')->append(cv\ht('small', $user->handle)));
        $container->push(cv\ht('img')->addOption('src', $user->getGravatarImagePath(50))->addOption('style', 'width: 50px; height: 50px;'));

        $roles_container = div();
        $roles_container->append(h2('Roles inhabited'));
        $ul = cv\ht('ul');
        foreach ($user->roles as $role)
        {
            $ul->append(cv\ht('li', $role->label . ' ')->append(cv\ht('span', $role->name)->addClass('muted')));
        }
        $roles_container->append($ul);
        $container->push($roles_container);

        $grid = grid();
        $container->push($grid);
        $row = $grid->row();

        $custom_rules = get_custom_markup_rules($this->app->getService('app.list'));
        $activity_renderers = get_activity_renderers($this->app->getService('app.list'));

        $external_uids = [];

        $disqQuery = $this->buildForumQuery();
        $disqs = $this->fetchDiscussions([$user->uid]);
        $external_uids = array_merge($external_uids, mpull($disqs, 'authorId'));

        $activity_query = new ActivityQuery($this->externalApp('activity'));
        $activities = $activity_query->retrieveActivitiesByUsers([$user->uid], 50);

        $external_uids = array_merge($external_uids, activity_get_external_uids($activities, $activity_renderers));
        $external_uids = array_merge($external_uids, ppull($activities, 'actor_uid'));

        $task_query = new TaskQuery($this->externalApp('task'));
        $tasks = mgroup($task_query->retrieveTasksForAssigned([$user->uid], null, 10), 'closed');
        $assigned_objs = mpull(array_mergev($tasks), 'assigned');
        $assigned_objs = array_mergev($assigned_objs);
        $external_uids = array_merge($external_uids, mpull($assigned_objs, 'userId'));

        $grouped_external_uids = group($external_uids, 'uid_get_type');

        $external_user_uids = idx($grouped_external_uids, 'USER', []);
        $external_user_objects = $query->retrieveUsersForUIDs($external_user_uids);
        pull($disqs, function ($disq) use ($external_user_objects) { $disq->setAuthor(idx($external_user_objects, $disq->authorId)); });
        pull($assigned_objs, function ($assigned) use ($external_user_objects) { $assigned->setUser(idx($external_user_objects, $assigned->userId)); });
        pull($activities, function ($activity) use ($external_user_objects) { $activity->actor_object = idx($external_user_objects, $activity->actor_uid); });

        $tag_query = create_tag_query($this->externalApp('tag'));
        fetch_external_tags(array_mergev(pull(array_mergev($tasks), function ($x) {return $x->tags->toArray();}), pull($disqs, function ($x) {return $x->tags->toArray();})), $tag_query);

        $disqQuery->fetchExternalsForDiscussions($disqs);
        $postCounts = $disqQuery->fetchPostCountsForDiscussions($disqs);

        // Discussions
        $listing = render_disq_listing($disqs, $postCounts, cv\hsprintf('Discussions started by <em>%s</em>', $user->name));
        $row->column(6)->push($listing);

        // Tasks
        $row->column(6)
            ->push(render_task_listing(idx($tasks, 0, []), 'Recent assigned tasks'))
            ->push(render_task_listing(idx($tasks, 1, []), 'Recent closed tasks'))
        ;

        // Activities
        $other = [
            'markup_rules' => $custom_rules,
            'users'        => $external_user_objects,
        ];
        $container->push(render_activity_listing($activities, $activity_renderers, $other, 'Recent activity')->addClass('feed-activity-listing'));

        $this->resMgr
            ->requireCss('application-activity-listing')
            ->requireCss('application-task-listing')
        ;

        return $payload;
    }

    private function buildForumQuery()
    {
        $query = new DiscussionQuery($this->externalApp('forum'));
        $query->addExternalQueryFromApplication(DiscussionQuery::EXT_QUERY_TAG, $this->externalApp('tag'));
        $query->addExternalQueryFromApplication(DiscussionQuery::EXT_QUERY_USER, $this->externalApp('user'));
        return $query;
    }

    private function fetchDiscussions(array $author_ids, $limit = 10, $offset = null, DiscussionQuery $query = null)
    {
        $query = $query ?: $this->buildForumQuery();
        $disqs = $query->retrieveDiscussionForAuthorUIDs($author_ids, $limit, $offset);
        return $disqs;
    }
}
