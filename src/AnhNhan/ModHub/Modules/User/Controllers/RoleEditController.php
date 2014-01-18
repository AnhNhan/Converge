<?php
namespace AnhNhan\ModHub\Modules\User\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\User\Storage\Role;
use AnhNhan\ModHub\Modules\User\Storage\RoleTransaction;
use AnhNhan\ModHub\Modules\User\Transaction\RoleTransactionEditor;
use AnhNhan\ModHub\Modules\User\Query\RoleQuery;
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
final class RoleEditController extends AbstractUserController
{
    public function handle()
    {
        $request = $this->request;
        $requestMethod = $request->getMethod();

        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);
        $query = new RoleQuery($this->app);

        $errors = array();

        if ($roleId = $request->request->get('id')) {
            $role = $query->retrieveRole("ROLE-" . $roleId);
        } else {
            $role = new Role;
        }

        if ($requestMethod == "POST") {
            $name        = trim($request->request->get("name"));
            $label       = trim($request->request->get("label"));
            $description = trim($request->request->get("description"));

            if (empty($name) || empty($label)) {
                $errors[] = "Text is empty!";
            }

            if (!$errors) {
                $app = $this->app;
                $em = $app->getEntityManager();

                $editor = RoleTransactionEditor::create($em)
                    ->setActor(\AnhNhan\ModHub\Storage\Types\UID::generate("USER"))
                    ->setEntity($role)
                    ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_SKIP)
                ;

                if (!$role->uid) {
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

        if ($errors) {
            $container->push(id(new Panel)
                ->setHeader(ModHub\ht("h2", "Sorry, there had been an error"))
                ->append(ModHub\ht("p",
                    "Either something happened, or you've missed something " .
                    "important, like left a field blank. I'm too lazy to write " .
                    "appropriate error-checking code to give you more details :P."
                ))
            );
        }

        $form = id(new FormView)
            ->setTitle($role->uid ? "Edit role '{$role->label}'" : "Create a new role")
            ->setAction($request->getPathInfo())
            ->setMethod("POST")
            ->append(id(new TextControl)
                ->setLabel("Name")
                ->setName("name")
                ->setValue($role->name)
                ->addOption("disabled", $role->name ? "disabled" : null))
            ->append(id(new TextControl)
                ->setLabel("Label")
                ->setName("label")
                ->setValue($role->label))
            ->append(id(new TextAreaControl)
                ->setLabel("Description")
                ->setName("description")
                ->setValue($role->description)
                ->addClass("forum-markup-processing-form")
            )
            ->append(id(new SubmitControl)
                ->addCancelButton("/roles/" . $role->cleanId)
                ->addSubmitButton("Hasta la vista!")
            )
            ->append(ModHub\ht("div", "Foo")->addClass("markup-preview-output"))
        ;
        $container->push($form);

        $this->app->getService("resource_manager")
            ->requireJs("application-forum-markup-preview");

        return $payload;
    }
}
