<?php

require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\Converge as cv;

use AnhNhan\Converge\Modules\Task\TaskApplication;
use AnhNhan\Converge\Modules\Task\Storage\Task;
use AnhNhan\Converge\Modules\Task\Storage\TaskTransaction;
use AnhNhan\Converge\Modules\Task\Storage\TaskPriority;
use AnhNhan\Converge\Modules\Task\Storage\TaskPriorityTransaction;
use AnhNhan\Converge\Modules\Task\Storage\TaskStatus;
use AnhNhan\Converge\Modules\Task\Storage\TaskStatusTransaction;
use AnhNhan\Converge\Modules\Task\Transaction\TaskEditor;
use AnhNhan\Converge\Modules\Task\Transaction\TaskStatusEditor;
use AnhNhan\Converge\Modules\Task\Transaction\TaskPriorityEditor;

use AnhNhan\Converge\Modules\User\UserApplication;

use AnhNhan\Converge\Modules\Task\Query\TaskQuery;
use AnhNhan\Converge\Modules\User\Query\UserQuery;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Types\UID;

use Symfony\Component\Yaml\Yaml;

$container = \AnhNhan\Converge\Web\Core::loadSfDIContainer();

$taskApp = new TaskApplication;
$taskApp->setContainer($container);
$taskEm  = $taskApp->getEntityManager();
$taskRepo = $taskEm->getRepository('AnhNhan\Converge\Modules\Task\Storage\Task');
$taskStatusRepo = $taskEm->getRepository('AnhNhan\Converge\Modules\Task\Storage\TaskStatus');
$taskPriorityRepo = $taskEm->getRepository('AnhNhan\Converge\Modules\Task\Storage\TaskPriority');
$taskQuery = new TaskQuery($taskEm);

$defaultTasksConfigPath = cv\get_root_super() . 'resources/default.task.props.yml';
$parsed = Yaml::parse($defaultTasksConfigPath);
$taskPropContainter = $parsed['tasks'];

$userQuery = new UserQuery(id(new UserApplication)->setContainer($container));
$anh_nhan  = $userQuery->retrieveUsersForCanonicalNames(['anhnhan']);
$anh_nhan  = idx($anh_nhan, 0);
if (!$anh_nhan)
{
    throw new Exception('User Anh Nhan does not exist.');
}

echo "Using: {$defaultTasksConfigPath}\n\n";

foreach ($taskPropContainter as $type => $_props)
{
    $is_status = $type == 'status';
    $query_method = $is_status ? 'retrieveTaskStatusForLabels' : 'retrieveTaskPriorityForLabels';
    $labels = ipull($_props, 'label');
    $labels_canonical = array_map('to_canonical', $labels);
    $existing_props = mkey($taskQuery->$query_method($labels_canonical), 'label_canonical');

    $entity_type = $is_status ? 'AnhNhan\Converge\Modules\Task\Storage\TaskStatus' : 'AnhNhan\Converge\Modules\Task\Storage\TaskPriority';
    $editor_type = $is_status ? 'AnhNhan\Converge\Modules\Task\Transaction\TaskStatusEditor' : 'AnhNhan\Converge\Modules\Task\Transaction\TaskPriorityEditor';
    $xact_type   = $is_status ? 'AnhNhan\Converge\Modules\Task\Storage\TaskStatusTransaction' : 'AnhNhan\Converge\Modules\Task\Storage\TaskPriorityTransaction';

    foreach ($_props as $property)
    {
        $label = $property['label'];
        $label_canonical = to_canonical($label);
        $color = idx($property, 'color');
        $order = idx($property, 'order');
        if (isset($existing_props[$property['label']]))
        {
            echo " [S] - Found task {$type} '{$property['label']}', doing nothing\n";
            continue;
        }

        $entity = new $entity_type;

        $editor = $editor_type::create($taskEm)
            ->setActor($anh_nhan->uid)
            ->setEntity($entity)
            ->setBehaviourOnNoEffect($editor_type::NO_EFFECT_SKIP)
            ->setFlushBehaviour($editor_type::FLUSH_DONT_FLUSH)
            ->addTransaction(
                $xact_type::create(TransactionEntity::TYPE_CREATE, $label)
            )
            ->addTransaction(
                $xact_type::create($xact_type::TYPE_EDIT_LABEL, $label)
            )
        ;

        if ($color)
        {
            $editor->addTransaction(
                $xact_type::create($xact_type::TYPE_EDIT_COLOR, $color)
            );
        }

        if ($order)
        {
            $editor->addTransaction(
                $xact_type::create($xact_type::TYPE_EDIT_ORDER, $order)
            );
        }

        $editor->apply();

        echo " [I] - Inserted task {$type} '{$label}'\n";
    }
    echo "\n";
}

$taskEm->flush();

echo "\nDone.\n";
