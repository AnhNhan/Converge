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

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TaskComment extends AbstractTaskController
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

        $inputText = $request->request->get('comment');

        if (!$inputText) {
            throw new \Exception("Input 'comment' can't be empty!");
        }
        $inputText = cv\normalize_newlines($inputText);

        $em = $this->app->getEntityManager();

        $editor = TaskEditor::create($em)
            ->setActor($this->user->uid)
            ->setEntity($task)
            ->setBehaviourOnNoEffect(TransactionEditor::NO_EFFECT_ERROR)
            ->addTransaction(TaskTransaction::create(TaskTransaction::TYPE_ADD_COMMENT, $inputText))
        ;

        $activityRecorder = new TaskRecorder($this->externalApp('activity'));
        $activityRecorder->record($editor->apply());

        $targetURI = "/task/" . $task->label_canonical;
        return new RedirectResponse($targetURI);
    }
}
