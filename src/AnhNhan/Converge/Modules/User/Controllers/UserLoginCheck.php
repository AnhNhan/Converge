<?php
namespace AnhNhan\Converge\Modules\User\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\User\Storage\User;
use AnhNhan\Converge\Modules\User\Storage\UserTransaction;
use AnhNhan\Converge\Modules\User\Transaction\UserTransactionEditor;
use AnhNhan\Converge\Modules\User\Storage\Email;
use AnhNhan\Converge\Modules\User\Query\UserQuery;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

use AnhNhan\Converge\Views\Form\FormView;
use AnhNhan\Converge\Views\Form\Controls\SubmitControl;
use AnhNhan\Converge\Views\Form\Controls\TextAreaControl;
use AnhNhan\Converge\Views\Form\Controls\TextControl;
use AnhNhan\Converge\Views\Grid\Grid;
use AnhNhan\Converge\Views\Panel\Panel;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class UserLoginCheck extends AbstractUserController
{
    public function handle()
    {
        $request = $this->request;

        $input_username = trim($request->request->get('_username'));
        $input_password = trim($request->request->get('_password'));

        assert(strlen($input_password));
        assert(strlen($input_username));

        $security_context = $this->app->getService('security.context');
        $provider_key = $this->app->getServiceParameter('security.provider_key');
        $token = new UsernamePasswordToken($input_username, $input_password, $provider_key);
        $security_context->setToken($token);

        // TODO: Allow for failure, handle it accordingly
        assert(!$security_context->getToken()->isAuthenticated());
        assert($security_context->isGranted(\Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter::IS_AUTHENTICATED_FULLY));
        assert($token != $security_context->getToken());
        assert( $security_context->getToken()->isAuthenticated());

        $session = $this->app->getService('session');
        $session->set('_security_token', $security_context->getToken());

        return new RedirectResponse($request->request->get('_target_path', '/'));
    }
}