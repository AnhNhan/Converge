<?php
namespace AnhNhan\Converge\Modules\Task\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use AnhNhan\Converge\Modules\Task\Activity\TaskRecorder;
use AnhNhan\Converge\Modules\Task\Storage\TaskTransaction;
use AnhNhan\Converge\Modules\Task\Storage\TaskBlocker;
use AnhNhan\Converge\Modules\Task\Storage\TaskSubTask;
use AnhNhan\Converge\Modules\Task\Transaction\TaskEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskAssoc extends AbstractTaskController
{
    public function requiredUserRoles($request)
    {
        return [
            'ROLE_USER',
        ];
    }

    public function handle()
    {
        $request = $this->request();
        $requestMethod = $request->getMethod();
        assert($requestMethod == 'POST');

        $query = $this->buildQuery();
        $task = $this->retrieveTaskObject($request, $query);
        if (!$task)
        {
            return id(new ResponseHtml404)->setText('This is not the task you are looking for.');
        }

        $enum_type = [
            'taskblocker' => true,
            'tasksubtask' => true,
        ];

        $enum_action = [
            'assoc' => true,
            'deassoc' => true,
        ];

        $action = $request->request->get('action');
        $type = $request->request->get('type');
        $parent_uid = $request->request->get('parent_uid');
        $child_uid = $request->request->get('child_uid');

        if ($parent_uid != $task->uid && $child_uid != $task->uid)
        {
            var_dump($parent_uid, $child_uid, $task->uid);
            return id(new ResponseHtml404)->setText('Wrong data sent here?');
        }

        if (!($parent_uid && $child_uid))
        {
            throw new \Exception('Invalid UIDs provided');
        }

        if (!isset($enum_action[$action]))
        {
            throw new \Exception('Invalid action');
        }

        if (!isset($enum_type[$type]))
        {
            throw new \Exception('Invalid type');
        }

        $tasks = mkey($query->retrieveTasksForUids([$parent_uid, $child_uid]), 'uid');
        if (count($tasks) != 2)
        {
            throw new \Exception('One or more tasks do not exist');
        }

        if ($action == 'assoc')
        {
            $class = $type == 'taskblocker' ? 'AnhNhan\Converge\Modules\Task\Storage\TaskBlocker' : 'AnhNhan\Converge\Modules\Task\Storage\TaskSubTask';
            $object = new $class($tasks[$parent_uid], $tasks[$child_uid]);
            $xact_type = TaskTransaction::TYPE_ADD_RELATION;
        }
        else
        {
            $method = $type == 'taskblocker' ? 'searchBlocker' : 'searchSubTask';
            $object = head($query->$method($tasks[$parent_uid], $tasks[$child_uid]));
            $xact_type = TaskTransaction::TYPE_DEL_RELATION;
        }

        $em = $this->app->getEntityManager();
        $activityRecorder = new TaskRecorder($this->externalApp('activity'));

        $editor_1 = TaskEditor::create($em)
            ->setActor($this->user->uid)
            ->setEntity(array_pop($tasks))
            ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_ERROR)
            ->addTransaction(TaskTransaction::create($xact_type, $object))
        ;
        $activityRecorder->record($editor_1->apply());

        $editor_2 = TaskEditor::create($em)
            ->setActor($this->user->uid)
            ->setEntity(array_pop($tasks))
            ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_ERROR)
            ->addTransaction(TaskTransaction::create($xact_type, $object))
        ;
        $activityRecorder->record($editor_2->apply());

        $targetURI = $request->query->has('return_to')
            ? $request->query->get('return_to')
            : '/task/' . $task->label_canonical;
        return new RedirectResponse($targetURI);
    }
}
