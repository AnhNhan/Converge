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

use AnhNhan\ModHub\Views\Web\Response\ResponseHtml404;

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
            if (!$role)
            {
                return id(new ResponseHtml404)->setText('Could not find that role.');
            }
        } else {
            $role = new Role;
        }

        $role_uid         = $role->uid;
        $role_cleanId     = $role->cleanId;
        $role_name        = $role->name;
        $role_label       = $role->label;
        $role_description = $role->description;

        if ($requestMethod == "POST") {
            $role_name        = trim($request->request->get("name"));
            $role_label       = trim($request->request->get("label"));
            $role_description = trim($request->request->get("description"));

            if (empty($role_name) || empty($role_label)) {
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
                        RoleTransaction::create(TransactionEntity::TYPE_CREATE, $role_name)
                    );
                }

                $editor
                    ->addTransaction(
                        RoleTransaction::create(RoleTransaction::TYPE_EDIT_LABEL, $role_label)
                    )
                    ->addTransaction(
                        RoleTransaction::create(RoleTransaction::TYPE_EDIT_DESC, $role_description)
                    )
                ;

                try
                {
                    $editor->apply();
                }
                catch (\Doctrine\DBAL\Exception\DuplicateKeyException $e)
                {
                    $errors[] = ModHub\safeHtml("A role with the name <em>'{$role_name}'</em> or label <em>'{$role_label}'</em> already exists.");
                    goto form_rendering;
                }

                $targetURI = "/role/" . $role->cleanId;
                return new RedirectResponse($targetURI);
            }
        }

        form_rendering:

        if ($errors) {
            $panel = id(new Panel)
                ->setHeader(ModHub\ht("h2", "Sorry, there had been an error"))
                ->append(ModHub\ht("p", "We can't continue until these issue(s) have been resolved:"))
            ;
            $list = ModHub\ht("ul");
            foreach ($errors as $e) {
                $list->appendContent(ModHub\ht("li", $e));
            }
            $panel->append($list);
            $container->push($panel);
        }

        $form = id(new FormView)
            ->setTitle($role_uid ? "Edit role '{$role_label}'" : "Create a new role")
            ->setAction($request->getPathInfo())
            ->setMethod("POST")
            ->append(id(new TextControl)
                ->setLabel("Name")
                ->setName("name")
                ->setValue($role_name)
                ->addOption("disabled", $role_uid ? "disabled" : null))
            ->append(id(new TextControl)
                ->setLabel("Label")
                ->setName("label")
                ->setValue($role_label))
            ->append(id(new TextAreaControl)
                ->setLabel("Description")
                ->setName("description")
                ->setValue($role_description)
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
