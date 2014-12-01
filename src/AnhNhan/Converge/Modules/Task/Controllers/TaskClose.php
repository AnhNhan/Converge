<?php
namespace AnhNhan\Converge\Modules\Task\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;

use AnhNhan\Converge\Modules\Task\Storage\TaskTransaction;
use AnhNhan\Converge\Modules\Task\Transaction\TaskEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskClose extends AbstractTaskController
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

        if (!$request->request->has('closed'))
        {
            throw new \Exception('We require the closed query parameter to be set!');
        }

        $closed = $request->request->get('closed');

        $em = $this->app->getEntityManager();

        $editor = TaskEditor::create($em)
            ->setActor($this->user->uid)
            ->setEntity($task)
            ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_ERROR)
            ->addTransaction(TaskTransaction::create(TaskTransaction::TYPE_EDIT_CLOSED, $closed))
        ;

        $xacts = $editor->apply();
        $this->dispatchEvent(Event_TaskTransaction_Record, arrayDataEvent($xacts));

        $targetURI = "/task/" . $task->label_canonical;
        return new RedirectResponse($targetURI);
    }
}
