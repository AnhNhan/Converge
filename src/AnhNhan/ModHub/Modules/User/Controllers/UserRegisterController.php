<?php
namespace AnhNhan\ModHub\Modules\User\Controllers;

use AnhNhan\ModHub as mh;
use AnhNhan\ModHub\Modules\User\Storage\User;
use AnhNhan\ModHub\Modules\User\Storage\UserTransaction;
use AnhNhan\ModHub\Modules\User\Transaction\UserTransactionEditor;
use AnhNhan\ModHub\Modules\User\Storage\Email;
use AnhNhan\ModHub\Modules\User\Query\RoleQuery;
use AnhNhan\ModHub\Modules\User\Query\UserQuery;
use AnhNhan\ModHub\Storage\Transaction\TransactionEditor;
use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;

use AnhNhan\ModHub\Views\Form\FormView;
use AnhNhan\ModHub\Views\Form\Controls\SubmitControl;
use AnhNhan\ModHub\Views\Form\Controls\TextAreaControl;
use AnhNhan\ModHub\Views\Form\Controls\TextControl;
use AnhNhan\ModHub\Views\Grid\Grid;
use AnhNhan\ModHub\Views\Panel\Panel;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class UserRegisterController extends AbstractUserController
{
    public function handle()
    {
        $request = $this->request;
        $requestMethod = $request->getMethod();

        $errors = array();

        $username  = trim($request->request->get("username"));
        $email     = trim($request->request->get("email"));
        $password  = trim($request->request->get("password"));

        $email = strtolower($email);
        $canon_name = User::to_canonical($username);

        if ($requestMethod == "POST")
        {
            if (empty($username) || empty($email))
            {
                $errors[] = "One or more texts are empty.";
            }
            if (empty($canon_name))
            {
                $errors[] = "Your canonical name would be empty. Other users would not be able to address you. Please choose another name.";
            }
            if (strlen($canon_name) < 3)
            {
                $errors[] = "Your canonical name would be too short ({$canon_name}). Please choose another name.";
            }
            if (strlen($username) > 60)
            {
                $errors[] = "Your username is too long. Max 60 letters, please.";
            }
            // Simple pw constraint - don't make it too elaborate
            if (strlen($password) < 6)
            {
                $errors[] = "Trust us, you want to set a longer password.";
            }

            $em = $this->app->getEntityManager();
            //if (!$errors)
            //{
                $query = new UserQuery($em);

                $_user  = $query->retrieveUsersForCanonicalNames([$canon_name], 1);
                $_email = $query->retrieveEmailsForNames([$email], 1);

                if ($_user)
                {
                    $errors[] = "A user with a similar name already exists.";
                }
                if ($_email)
                {
                    $errors[] = "Another user already has this email occupied.";
                }
            //}

            if (!$errors)
            {
                $obj_user = new User;
                $obj_email = new Email;

                // Just saving us some elaborated code
                // TODO: Do this properly
                $obj_email->email = $email;
                $obj_email->user  = $obj_user;
                $obj_email->is_verified = true;
                $obj_email->is_primary  = true;

                // Set primary email of user object
                $userReflProp = $em->getClassMetadata(get_class($obj_user))
                    ->reflClass->getProperty('primary_email');
                $userReflProp->setAccessible(true);
                $userReflProp->setValue(
                    $obj_user, $email
                );

                $salt = \Filesystem::readRandomCharacters(30);
                $pwEncoderFactory = $this->app->getService('security.encoder.factory');
                $pwEncoder        = $pwEncoderFactory->getEncoder($obj_user);
                $pw               = $pwEncoder->encodePassword($password, $salt);
                $xact_pw          = $salt . UserTransactionEditor::SALT_PW_SEPARATOR . $pw;

                $roleQuery = new RoleQuery($em);
                $role      = idx($roleQuery->retrieveRolesForNames(['ROLE_USER'], 1), 0);
                if (!$role)
                {
                    throw new \LogicException('Something\'s terribly wrong here. Seeded default roles?');
                }

                $editor = UserTransactionEditor::create($em)
                    ->setActor(User::USER_UID_NONE)
                    ->setEntity($obj_user)
                    ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_SKIP)
                ;

                $editor
                    ->addTransaction(
                        UserTransaction::create(TransactionEntity::TYPE_CREATE, $username)
                    )
                    ->addTransaction(
                        UserTransaction::create(UserTransaction::TYPE_EDIT_PASSWORD, $xact_pw)
                    )
                    ->addTransaction(
                        UserTransaction::create(UserTransaction::TYPE_ADD_EMAIL, $email)
                    )
                    ->addTransaction(
                        UserTransaction::create(UserTransaction::TYPE_ADD_ROLE, $role->uid)
                    )
                ;

                $em->beginTransaction();
                try {
                    $editor->apply();
                    $em->flush();
                    $em->persist($obj_email);
                    $em->flush();

                    $em->commit();
                } catch (\Exception $e) {
                    $em->rollback();
                    ob_start();
                    var_dump($e);
                    $errors[] = mh\safeHtml(ob_get_clean());
                    $errors[] = "Something happened. No idea.";
                    goto pre_form;
                }

                $targetURI = "/";
                return new RedirectResponse($targetURI);
            }
        }

        pre_form:

        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);
        $payload->setTitle("Join us!");

        $panel = null;
        if ($errors)
        {
            $panel = new Panel;
            $panel->setHeader(h2("Oops, something looks wrong"));
            $panel->append(mh\ht("p", "We can't continue until these issue(s) have been resolved:"));

            $list = mh\ht("ul");
            foreach ($errors as $e) {
                $list->appendContent(mh\ht("li", $e));
            }
            $panel->append($list);
        }

        // TODO: Convert this into actual code
        $form = mh\hsprintf(<<<EOT
<div class="width6" style="margin-left: 25%%;">
<h1>Heysa!</h1>
<h2>We've been waiting for you! <small>Let's get started.</small></h2>
{$panel}
<form class="form form-dual-column user-join-form" action="/join" method="POST">
    <div class="form-control-container">
        <div class="form-control-label">Email</div>
        <div class="form-control-element form-control-grouped"><span class="form-control-extra">@</span><input class="form-control form-control-text" name="email" value="{$email}" type="text" placeholder="someone@example.com"></div>
    </div>
    <div class="form-control-container">
        <div class="form-control-label">Username</div>
        <div class="form-control-element form-control-grouped"><input class="form-control form-control-text" name="username" value="{$username}" type="text" placeholder="SomeName"></div>
    </div>
    <div class="form-control-container">
        <div class="form-control-label">Password</div>
        <div class="form-control-element form-control-grouped"><input class="form-control form-control-text" name="password" value="{$password}" type="password"></div>
    </div>
    <div class="form-control-container form-control-submit">
        <a href="/" class="btn btn-default"><i class="ion-close"></i> Cancel</a>
        <button name="__submit__" class="btn btn-primary">Hasta la vista! <i class="ion-checkmark"></i></button>
    </div>
    <input name="__form__" value="1" type="hidden">
</form>
</div>
EOT
        );
        $container->push($form);

        $this->app->getService("resource_manager")
            ->requireJs("application-forum-markup-preview");

        return $payload;
    }
}
