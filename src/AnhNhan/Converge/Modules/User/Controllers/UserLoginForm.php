<?php
namespace AnhNhan\Converge\Modules\User\Controllers;

use AnhNhan\Converge as cv;

use AnhNhan\Converge\Views\Form\FormView;
use AnhNhan\Converge\Views\Form\Controls\SubmitControl;
use AnhNhan\Converge\Views\Form\Controls\PasswordControl;
use AnhNhan\Converge\Views\Form\Controls\TextControl;
use AnhNhan\Converge\Views\Form\Controls\HiddenControl;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class UserLoginForm extends AbstractUserController
{
    public function handle()
    {
        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);
        $request = $this->request;

        $referer = preg_replace('@http[s]?://.*?/@i', '', idx($_SERVER, 'HTTP_REFERER', '/'));
        $return_url = $request->get('return_to', $referer);

        $username = '';
        $password = '';

        $form = id(new FormView)
            ->setTitle('Login')
            ->setAction('login_check')
            ->setMethod('POST')
            ->addOption('style', 'width: 50%;')
            ->append(id(new TextControl)
                ->setLabel('Username')
                ->setName('_username')
                ->setValue($username))
            ->append(id(new PasswordControl)
                ->setLabel('Password')
                ->setName('_password')
                ->setValue($password))
            ->append(id(new HiddenControl)
                ->setName('_target_path')
                ->setValue($return_url))
            ->append(id(new SubmitControl)
                ->addCancelButton('/')
                ->addSubmitButton('Once more unto the breach!')
            )
        ;
        $container->push($form);

        return $payload;
    }
}
