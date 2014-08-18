<?php
namespace AnhNhan\Converge\Modules\Task\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use AnhNhan\Converge\Modules\Task\Storage\TaskTransaction;
use AnhNhan\Converge\Modules\Task\Transaction\TaskEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

use AnhNhan\Converge\Views\Form\FormView;
use AnhNhan\Converge\Views\Form\Controls\SelectControl;
use AnhNhan\Converge\Views\Form\Controls\SubmitControl;
use AnhNhan\Converge\Views\Form\Controls\TextAreaControl;
use AnhNhan\Converge\Views\Form\Controls\TextControl;
use AnhNhan\Converge\Views\Grid\Grid;
use AnhNhan\Converge\Views\Panel\Panel;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskEdit extends AbstractTaskController
{
    public function handle()
    {
        $request = $this->request;
        $requestMethod = $request->getMethod();
        $query = $this->buildQuery();

        $task = $this->retrieveTaskObject($request, $query);
        if (!$task)
        {
            return id(new ResponseHtml404)->setText('This is not the task you are looking for.');
        }
        $user_query = create_user_query($this->externalApp('user'));
        fetch_external_authors([$task], $user_query, 'assignedId', 'setAssigned', 'assigned');

        $priorities = mkey($query->retrieveTaskPriorities(), 'label_canonical');
        $statuses   = mkey($query->retrieveTaskStatus(), 'label_canonical');

        $container = new MarkupContainer;

        $errors = [];

        $task_uid = $task->uid;
        $task_label = $task->label;
        $task_assigned = $task->assignedId ? $task->assigned->canonical_name : null;
        $task_status = $task->status ? $task->status->label_canonical : null;
        $task_priority = $task->priority ? $task->priority->label_canonical : null;
        $task_description = $task->description;

        if ($requestMethod == 'POST')
        {
            $task_label = trim($request->request->get('label'));
            $task_description = trim($request->request->get('description'));

            $task_assigned = trim($request->request->get('assigned'));
            $task_assigned = to_canonical($task_assigned);

            $task_priority = trim($request->request->get('priority'));
            $task_priority = to_canonical($task_priority);

            $task_status = trim($request->request->get('status'));
            $task_status = to_canonical($task_status);

            $task_label_canonical = to_canonical($task_label);

            if (!strlen($task_label))
            {
                $errors[] = 'We require a descriptive label';
            }
            else if (strlen($task_label_canonical) < 3)
            {
                $errors[] = 'Label is too short to be of any significance.';
            }

            if (!isset($priorities[$task_priority]))
            {
                $errors[] = 'Invalid task priority';
            }

            if (!isset($statuses[$task_status]))
            {
                $errors[] = 'Invalid task status';
            }

            $task_assigned_object = idx($user_query->retrieveUsersForCanonicalNames([$task_assigned]), 0);
            if ($task_assigned && !$task_assigned_object)
            {
                $errors[] = cv\hsprintf(
                    'Could not find the user %s',
                    tooltip('span', '@' . $task_assigned, 'user not found')->addClass('bad-username')
                );
            }

            if (!$errors)
            {
                $task_priority_object = $priorities[$task_priority];
                $task_status_object   = $statuses[$task_status];

                $em = $this->app->getEntityManager();

                $editor = TaskEditor::create($em)
                    ->setActor($this->user->uid)
                    ->setEntity($task)
                    ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_SKIP)
                ;

                if (!$task->uid) {
                    $editor->addTransaction(
                        TaskTransaction::create(TransactionEntity::TYPE_CREATE, $task_label)
                    );
                }

                $editor
                    ->addTransaction(
                        TaskTransaction::create(TaskTransaction::TYPE_EDIT_LABEL, $task_label)
                    )
                    ->addTransaction(
                        TaskTransaction::create(TaskTransaction::TYPE_EDIT_DESC, $task_description)
                    )
                    ->addTransaction(
                        TaskTransaction::create(TaskTransaction::TYPE_EDIT_ASSIGN, $task_assigned_object->uid)
                    )
                    ->addTransaction(
                        TaskTransaction::create(TaskTransaction::TYPE_EDIT_STATUS, $task_status_object)
                    )
                    ->addTransaction(
                        TaskTransaction::create(TaskTransaction::TYPE_EDIT_PRIORITY, $task_priority_object)
                    )
                ;

                try
                {
                    $editor->apply();
                }
                catch (\Doctrine\DBAL\Exception\DuplicateKeyException $e)
                {
                    $errors[] = cv\safeHtml("A task with the label <em>'{$task_label}'</em> (or similar) already exists.");
                    goto form_rendering;
                }

                $targetURI = "/task/" . $task->cleanId;
                return new RedirectResponse($targetURI);
            }
        }

        form_rendering:

        if ($errors) {
            $panel = id(new Panel)
                ->setHeader(cv\ht('h2', 'Sorry, there had been an error'))
                ->append(cv\ht('p', 'We can\'t continue until these issue(s) have been resolved:'))
            ;
            $list = cv\ht('ul');
            foreach ($errors as $e) {
                $list->append(cv\ht('li', $e));
            }
            $panel->append($list);
            $container->push($panel);
        }

        $priority_control = id(new SelectControl)
            ->setLabel('Priority')
            ->setName('priority')
            ->setSelected($task_priority)
        ;
        foreach ($priorities as $priority)
        {
            $priority_control->addEntry([
                'label' => $priority->label,
                'value' => $priority->label_canonical,
            ]);
        }

        $status_control = id(new SelectControl)
            ->setLabel('Status')
            ->setName('status')
            ->setSelected($task_status)
        ;
        foreach ($statuses as $status)
        {
            $status_control->addEntry([
                'label' => $status->label,
                'value' => $status->label_canonical,
            ]);
        }

        $page_title = $task_uid ? "Edit task '{$task_label}'" : 'Create a new task';

        $form = id(new FormView)
            ->setTitle($page_title)
            ->setAction($request->getPathInfo())
            ->setMethod('POST')
            ->append(id(new TextControl)
                ->setLabel('Label')
                ->setName('label')
                ->setValue($task_label))
            ->append(id(new TextControl)
                ->setLabel('Assigned to')
                ->setName('assigned')
                ->setHelp('leave empty if unassigned')
                ->setValue($task_assigned))
            ->append($priority_control)
            ->append($status_control)
            ->append(id(new TextAreaControl)
                ->setLabel('Description')
                ->setName('description')
                ->setHelp('optional')
                ->setValue($task_description)
                ->addClass('forum-markup-processing-form')
            )
            ->append(id(new SubmitControl)
                ->addCancelButton('/task/')
                ->addSubmitButton('Hasta la vista!')
            )
            ->append(cv\ht('div', 'Foo')->addClass('markup-preview-output'))
        ;
        $container->push($form);

        $this->app->getService('resource_manager')
            ->requireJs('application-forum-markup-preview');

        $payload = new HtmlPayload;
        $payload->setTitle($page_title);
        $payload->setPayloadContents($container);
        return $payload;
    }
}
