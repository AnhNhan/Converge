<?php
namespace AnhNhan\Converge\Modules\User\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumListing;
use AnhNhan\Converge\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
use AnhNhan\Converge\Modules\User\Query\UserQuery;
use AnhNhan\Converge\Modules\User\Storage\User;
use AnhNhan\Converge\Views\Grid\Grid;
use AnhNhan\Converge\Views\Panel\Panel;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class UserDisplay extends AbstractUserController
{
    public function handle()
    {
        $request = $this->request;

        $req_canon_name = $request->get('name');

        $query = new UserQuery($this->app);
        $user  = idx($query->retrieveUsersForCanonicalNames([$req_canon_name], 1), 0);

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

        $disqQuery = $this->buildForumQuery();
        $disqs = $this->fetchDiscussions([$user->uid]);
        $disqQuery->fetchExternalsForDiscussions($disqs);
        $postCounts = $disqQuery->fetchPostCountsForDiscussions($disqs);

        $listing = render_disq_listing($disqs, $postCounts, cv\hsprintf('Discussions started by <em>%s</em>', $user->name));
        $container->push($listing);

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
