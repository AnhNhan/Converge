<?php
namespace AnhNhan\Converge\Modules\User\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class UserLogout extends AbstractUserController
{
    public function handle()
    {
        $this->app->getService('session')->invalidate();
        return new RedirectResponse('/');
    }
}
