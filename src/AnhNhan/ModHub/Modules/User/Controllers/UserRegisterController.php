<?php
namespace AnhNhan\ModHub\Modules\User\Controllers;

use AnhNhan\ModHub as mh;
use AnhNhan\ModHub\Modules\User\Storage\User;
use AnhNhan\ModHub\Modules\User\Storage\UserTransaction;
use AnhNhan\ModHub\Modules\User\Transaction\UserTransactionEditor;
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

        if ($requestMethod == "POST") {
            if (empty($username) || empty($email) || empty($password)) {
                $errors[] = "One or more texts are empty";
            }

            $em = $this->app->getEntityManager();
            if (!$errors) {
                // Check DB for validity
                $query = new UserQuery($em);
            }

            if (!$errors) {
                $user = new User;

                $editor = UserTransactionEditor::create($em)
                    ->setActor(User::USER_UID_NONE)
                    ->setEntity($user)
                    ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_SKIP)
                ;

                if (!$user->uid) {
                    $editor->addTransaction(
                        RoleTransaction::create(TransactionEntity::TYPE_CREATE, $name)
                    );
                }

                $editor
                    ->addTransaction(
                        RoleTransaction::create(RoleTransaction::TYPE_EDIT_LABEL, $label)
                    )
                    ->addTransaction(
                        RoleTransaction::create(RoleTransaction::TYPE_EDIT_DESC, $description)
                    )
                ;

                $editor->apply();

                $targetURI = "/role/" . $role->cleanId;
                return new RedirectResponse($targetURI);
            }
        }

        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);
        $payload->setTitle("Join us!");

        // TODO: Convert this into actual code
        $form = mh\hsprintf(<<<EOT
<div class="width6" style="margin-left: 25%%;">
<h1>Heysa!</h1>
<h2>We've been waiting for you! <small>Let's get started.</small></h2>
<form class="form form-dual-column user-join-form" action="/join" method="POST">
    <div class="form-control-container">
        <div class="form-control-label">Email</div>
        <div class="form-control-element form-control-grouped"><span class="form-control-extra">@</span><input class="form-control form-control-text" name="email" value="" type="text" placeholder="someone@example.com"></div>
    </div>
    <div class="form-control-container">
        <div class="form-control-label">Username</div>
        <div class="form-control-element form-control-grouped"><input class="form-control form-control-text" name="username" value="" type="text" placeholder="SomeName"></div>
    </div>
    <div class="form-control-container">
        <div class="form-control-label">Password</div>
        <div class="form-control-element form-control-grouped"><input class="form-control form-control-text" name="password" value="" type="password"></div>
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
