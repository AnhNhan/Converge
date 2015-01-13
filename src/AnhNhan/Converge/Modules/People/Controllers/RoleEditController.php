<?php
namespace AnhNhan\Converge\Modules\People\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\People\Storage\Role;
use AnhNhan\Converge\Modules\People\Storage\RoleTransaction;
use AnhNhan\Converge\Modules\People\Transaction\RoleTransactionEditor;
use AnhNhan\Converge\Modules\People\Query\RoleQuery;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

use AnhNhan\Converge\Views\Form\FormView;
use AnhNhan\Converge\Views\Form\Controls\SubmitControl;
use AnhNhan\Converge\Views\Form\Controls\TextAreaControl;
use AnhNhan\Converge\Views\Form\Controls\TextControl;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\HttpFoundation\RedirectResponse;

use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class RoleEditController extends AbstractPeopleController
{
    public function requiredUserRoles($request)
    {
        return [
            'ROLE_USER',
        ];
    }

    public function handle()
    {
        $request = $this->request;
        $requestMethod = $request->getMethod();

        $container = new MarkupContainer;
        $payload = $this->payload_html;
        $payload->setPayloadContents($container);
        $query = new RoleQuery($this->app);

        $errors = array();

        if ($roleId = $request->request->get('id')) {
            $role = $query->retrieveRole('ROLE-' . $roleId);
            if (!$role)
            {
                return (new ResponseHtml404)->setText('Could not find that role.');
            }
        } else {
            $role = new Role;
        }

        $role_uid         = $role->uid;
        $role_cleanId     = $role->cleanId;
        $role_name        = $role->name;
        $role_label       = $role->label;
        $role_description = $role->description;

        if ($requestMethod == 'POST') {
            $role_name        = trim($request->request->get('name'));
            $role_label       = trim($request->request->get('label'));
            $role_description = trim($request->request->get('description'));
            $role_description = Converge\normalize_newlines($role_description);

            if (empty($role_name) || empty($role_label)) {
                $errors[] = 'Text is empty!';
            }

            if (!$errors) {
                $app = $this->app;
                $em = $app->getEntityManager();

                $editor = RoleTransactionEditor::create($em)
                    ->setActor($this->user->uid)
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
                    $errors[] = Converge\safeHtml("A role with the name <em>'{$role_name}'</em> or label <em>'{$role_label}'</em> already exists.");
                    goto form_rendering;
                }

                $targetURI = '/role/' . $role->cleanId;
                return new RedirectResponse($targetURI);
            }
        }

        form_rendering:

        if ($errors) {
            $panel = panel(h2('Sorry, there had been an error'))
                ->append(Converge\ht('p', 'We can\'t continue until these issue(s) have been resolved:'))
            ;
            $list = Converge\ht('ul');
            foreach ($errors as $e) {
                $list->append(Converge\ht('li', $e));
            }
            $panel->append($list);
            $container->push($panel);
        }

        $form = form($role_uid ? "Edit role '{$role_label}'" : 'Create a new role', $request->getPathInfo(), 'POST')
            ->append(form_textcontrol('Name', 'name', $role_name)
                ->addOption('disabled', $role_uid ? 'disabled' : null))
            ->append(form_textcontrol('Label', 'label', $role_label))
            ->append(form_textareacontrol('Description', 'description', $role_description)
                ->addClass('forum-markup-processing-form')
            )
            ->append(form_submitcontrol('/roles/' . $role->cleanId, 'Hasta la vista!'))
            ->append(div('markup-preview-output', 'Foo'))
        ;
        $container->push($form);

        $this->app->getService('resource_manager')
            ->requireJs('application-forum-markup-preview');

        return $payload;
    }
}
