<?php
namespace AnhNhan\Converge\Modules\Task\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use AnhNhan\Converge\Modules\Tag\Views\FormControls\TagSelector;
use AnhNhan\Converge\Modules\Task\Activity\TaskRecorder;
use AnhNhan\Converge\Modules\Task\Storage\TaskBlocker;
use AnhNhan\Converge\Modules\Task\Storage\TaskSubTask;
use AnhNhan\Converge\Modules\Task\Storage\TaskTransaction;
use AnhNhan\Converge\Modules\Task\Transaction\TaskEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

use AnhNhan\Converge\Views\Form\Controls\DummyControl;
use AnhNhan\Converge\Views\Form\Controls\SelectControl;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskEdit extends AbstractTaskController
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
        $query = $this->buildQuery();

        $task = $this->retrieveTaskObject($request, $query);
        if (!$task)
        {
            return id(new ResponseHtml404)->setText('This is not the task you are looking for.');
        }
        $parent_task_id = $request->query->get('parent_task_id');
        $parent_tasks = [];
        $task->uid or $parent_tasks = $query->retrieveTasksForCanonicalLabels([$parent_task_id]);
        $parent_task = head($parent_tasks);

        $tasks = $parent_tasks;
        $tasks[] = $task;

        $user_query = create_user_query($this->externalApp('user'));
        fetch_external_authors(array_mergev(mpull($tasks, 'assigned')), $user_query, 'userId', 'setUser', 'user');
        $query->fetchExternalTags($tasks);

        $priorities = mkey($query->retrieveTaskPriorities(), 'label_canonical');
        $statuses   = mkey($query->retrieveTaskStatus(), 'label_canonical');

        $container = new MarkupContainer;

        $errors = [];

        $e_label = null;
        $e_assigned = null;
        $e_status = null;
        $e_priority = null;
        $e_tags = null;

        $assigned = mpull($task->assigned, 'user');

        $task_uid = $task->uid;
        $task_label = $task->label;
        $task_assigned = mpull($assigned, 'canonical_name');
        $task_status = $task->status ? $task->status->label_canonical : null;
        $task_priority = $task->priority ? $task->priority->label_canonical : null;
        $task_description = $task->description;
        $task_tags_orig = $task->tags ? mkey(mpull($task->tags->toArray(), 'tag'), 'uid') : [];
        $task_tags = mpull($task_tags_orig, 'label');

        $task_assigned_orig = mpull($assigned, null, 'canonical_name');

        $draft_key = 'create-task-description';
        if (!$task_uid && $requestMethod != 'POST')
        {
            $description_draft = $this->getDraftObject($draft_key);
            $description_draft_text = $description_draft ? $description_draft['contents'] : null;
            $description_draft_date = $description_draft ? 'Draft originally loaded on ' . date("h:i - D, d M 'y", $description_draft['modified_at']) : null;
            $task_description = $description_draft_text;
        }

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

            $_tags = $request->request->get("tags");

            $task_label_canonical = to_canonical($task_label);

            if (!strlen($task_label))
            {
                $errors[] = 'We require a descriptive label';
                $e_label = 'we really need this';
            }
            else if (strlen($task_label_canonical) < 3)
            {
                $errors[] = cv\hsprintf('Label is too short to be of any significance (we only understand it as \'<em>%s</em>\').', $task_label_canonical);
                $e_label = 'yeah, if you\'d add a little bit more content, that would be great';
            }

            if (!isset($priorities[$task_priority]))
            {
                $e_priority = 'invalid task priority';
                $errors[] = cv\hsprintf('The given priority \'<em>%s</em>\' is invalid', $task_priority);
            }

            if (!isset($statuses[$task_status]))
            {
                $e_status = 'invalid task status';
                $errors[] = cv\hsprintf('The given status \'<em>%s</em>\' is invalid', $task_status);
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
                $e_assigned = 'contains imaginary users';
            }

            $tag_result = validate_tags_from_form_input($_tags, $this->externalApp('tag'));
            if (is_array($tag_result))
            {
                $tag_objects = $tag_result;
                $task_tags = mpull($tag_objects, 'label', 'uid');
            }
            else
            {
                $errors[] = $tag_result;
                $e_tags = 'invalid value';
            }

            if (!$errors)
            {
                $task_priority_object = $priorities[$task_priority];
                $task_status_object   = $statuses[$task_status];

                $orig_tag_ids = array_keys($task_tags_orig);
                $curr_tag_ids = array_keys($task_tags);

                $new_tag_ids = array_diff($curr_tag_ids, $orig_tag_ids);
                $del_tag_ids = array_diff($orig_tag_ids, $curr_tag_ids);

                $em = $this->app->getEntityManager();

                $editor = TaskEditor::create($em)
                    ->setActor($this->user->uid)
                    ->setEntity($task)
                    ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_SKIP)
                    ->setFlushBehaviour(TransactionEditor::FLUSH_FLUSH)
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

                foreach ($new_tag_ids as $tag_id) {
                    $editor->addTransaction(
                        TaskTransaction::create(TaskTransaction::TYPE_ADD_TAG, $tag_id)
                    );
                }

                foreach ($del_tag_ids as $tag_id) {
                    $editor->addTransaction(
                        TaskTransaction::create(TaskTransaction::TYPE_DEL_TAG, $tag_id)
                    );
                }

                $em->beginTransaction();
                try
                {
                    $activityRecorder = new TaskRecorder($this->externalApp('activity'));
                    $activityRecorder->record($editor->apply());

                    if (!$task_uid)
                    {
                        $this->deleteDraftObject($draft_key);
                    }

                    if ($parent_task)
                    {
                        $this->internalSubRequest(
                            'task/assoc/' . $parent_task->label_canonical,
                            [
                                'action'     => 'assoc',
                                'type'       => 'tasksubtask',
                                'parent_uid' => $parent_task->uid,
                                'child_uid'  => $task->uid,
                            ],
                            'POST',
                            false
                        );
                    }

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
            $panel = panel(h2('Sorry, there had been an error'))
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
            ->setError($e_priority)
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
            ->setError($e_status)
        ;
        foreach ($statuses as $status)
        {
            $status_control->addEntry([
                'label' => $status->label,
                'value' => $status->label_canonical,
            ]);
        }

        if (is_array($task_tags)) {
            $task_tags = implode(", ", $task_tags);
        }

        $tag_selector = id(new TagSelector)
            ->addClass("disq-tag-selector")
            ->setLabel("Tags")
            ->setName("tags")
            ->setValue($task_tags)
            ->setError($e_tags)
        ;

        $page_title = $task_uid ? "Edit task '{$task_label}'" : 'Create a new task';

        $form = form($page_title, $request->getRequestUri(), 'POST');

        if ($parent_task)
        {
            $form->append(
                (new DummyControl(
                    render_task($parent_task, false, false)->addOption('style', 'font-size: 0.8em;')
                ))
                    ->setLabel('Parent Task')
                    ->addOption('style', 'height: auto;')
            );
        }

        $form
            ->append(form_textcontrol('Label', 'label', $task_label)->setError($e_label))
            ->append(form_textcontrol('Assigned to', 'assigned', implode(', ', $task_assigned))
                ->addOption('placeholder', 'nobody')
                ->setHelp('comma separated list; optional')
                ->setError($e_assigned))
            ->append($tag_selector)
            ->append($priority_control)
            ->append($status_control)
            ->append(form_textareacontrol('Description', 'description', $task_description)
                ->setHelp($description_draft_date ?: 'optional')
                ->addClass('forum-markup-processing-form')
                ->addOption('data-draft-key', !$task_uid ? $draft_key : null)
            )
            ->append(form_submitcontrol($task_uid ? 'task/' . $task->label_canonical : '/task/', 'Hasta la vista!'))
            ->append(div('markup-preview-output', 'Foo'))
        ;
        $container->push($form);

        $this->app->getService('resource_manager')
            ->requireJS('application-forum-tag-selector')
            ->requireJs('application-forum-markup-preview');

        $payload = $this->payload_html;
        $payload->setTitle($page_title);
        $payload->setPayloadContents($container);
        return $payload;
    }
}
