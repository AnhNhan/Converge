<?php
namespace AnhNhan\ModHub\Modules\User\Controllers;

use AnhNhan\ModHub as mh;

use AnhNhan\ModHub\Views\Form\FormView;
use AnhNhan\ModHub\Views\Form\Controls\SubmitControl;
use AnhNhan\ModHub\Views\Form\Controls\PasswordControl;
use AnhNhan\ModHub\Views\Form\Controls\TextControl;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
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
            ->append(id(new SubmitControl)
                ->addCancelButton('/')
                ->addSubmitButton('Once more unto the breach!')
            )
        ;
        $container->push($form);

        return $payload;
    }
}
