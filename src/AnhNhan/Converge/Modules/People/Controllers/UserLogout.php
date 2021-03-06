<?php
namespace AnhNhan\Converge\Modules\People\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class UserLogout extends AbstractPeopleController
{
    public function requiredUserRoles($request)
    {
        return [
            'ROLE_USER',
        ];
    }

    public function handle()
    {
        $this->app->getService('session')->invalidate();
        return new RedirectResponse('/');
    }
}
