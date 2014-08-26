<?php
namespace AnhNhan\Converge\Modules\Task\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use AnhNhan\Converge\Modules\Task\Activity\TaskRecorder;
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
        fetch_external_authors($task->assigned, $user_query, 'userId', 'setUser', 'user');

        $priorities = mkey($query->retrieveTaskPriorities(), 'label_canonical');
        $statuses   = mkey($query->retrieveTaskStatus(), 'label_canonical');

        $container = new MarkupContainer;

        $errors = [];

        $assigned = mpull($task->assigned, 'user');

        $task_uid = $task->uid;
        $task_label = $task->label;
        $task_assigned = mpull($assigned, 'canonical_name');
        $task_status = $task->status ? $task->status->label_canonical : null;
        $task_priority = $task->priority ? $task->priority->label_canonical : null;
        $task_description = $task->description;

        $task_assigned_orig = mpull($assigned, null, 'canonical_name');

        if ($requestMethod == 'POST')
        {
            $task_label = trim($request->request->get('label'));
            $task_description = trim($request->request->get('description'));
            $task_description = cv\normalize_newlines($task_description);

            $task_assigned = trim($request->request->get('assigned'));
            $task_assigned = array_map('to_canonical', explode(',', $task_assigned));
            $task_assigned = array_unique(array_filter($task_assigned));

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
                $errors[] = cv\hsprintf('Label is too short to be of any significance (we only understand it as \'<em>%s</em>\').', $task_label_canonical);
            }

            if (!isset($priorities[$task_priority]))
            {
                $errors[] = 'Invalid task priority';
            }

            if (!isset($statuses[$task_status]))
            {
                $errors[] = 'Invalid task status';
            }

            $task_assigned_objects = $user_query->retrieveUsersForCanonicalNames($task_assigned);
            $task_assigned_objects = mkey($task_assigned_objects, 'canonical_name');
            if ($task_assigned && count($task_assigned_objects) < count($task_assigned))
            {
                $errors[] = cv\hsprintf(
                    'Could not find user(s): %s',
                    phutil_implode_html(', ', array_map(
                        function ($x) { return phutil_safe_html(tooltip('span', '@' . $x, 'user not found')->addClass('bad-username')); },
                        array_diff($task_assigned, mpull($task_assigned_objects, 'canonical_name'))
                    ))
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
                ;

                $assigned_add = array_diff(array_keys($task_assigned_objects), array_keys($task_assigned_orig));
                $assigned_del = array_diff(array_keys($task_assigned_orig), array_keys($task_assigned_objects));

                if ($assigned_add || $assigned_del)
                {
                    foreach (['add' => $assigned_add, 'del' => $assigned_del] as $type => $usernames)
                    {
                        $is_add = $type == 'add';
                        $xact_type = $is_add ? TaskTransaction::TYPE_ADD_ASSIGN : TaskTransaction::TYPE_DEL_ASSIGN;
                        $user_source = $is_add ? $task_assigned_objects : $task_assigned_orig;
                        foreach (array_select_keys($user_source, $usernames) as $assigned_user)
                        {
                            $editor->addTransaction(
                                TaskTransaction::create($xact_type, $assigned_user->uid)
                            );
                        }
                    }
                }

                // Sadly equality is done by identity, not by contents :/
                if (!$task->status || $task_status != $task->status->label_canonical)
                {
                    $editor->addTransaction(
                        TaskTransaction::create(TaskTransaction::TYPE_EDIT_STATUS, $task_status_object)
                    );
                }
                if (!$task->priority || $task_priority != $task->priority->label_canonical)
                {
                    $editor->addTransaction(
                        TaskTransaction::create(TaskTransaction::TYPE_EDIT_PRIORITY, $task_priority_object)
                    );
                }

                $em->beginTransaction();
                try
                {
                    $activityRecorder = new TaskRecorder($this->externalApp('activity'));
                    $activityRecorder->record($editor->apply());
                    $em->commit();
                }
                catch (\Doctrine\DBAL\Exception\DuplicateKeyException $e)
                {
                    $em->rollback();
                    $errors[] = cv\safeHtml("A task with the label <em>'{$task_label}'</em> (or similar) already exists.");
                    goto form_rendering;
                }

                $targetURI = "/task/" . $task->label_canonical;
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
                ->setHelp('comma separated list')
                ->setValue(implode(', ', $task_assigned)))
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
                ->addCancelButton($task_uid ? 'task/' . $task->label_canonical : '/task/')
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
