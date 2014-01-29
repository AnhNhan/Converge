<?php

require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\ModHub as mh;

use AnhNhan\ModHub\Modules\User\UserApplication;
use AnhNhan\ModHub\Modules\User\Storage\Role;
use AnhNhan\ModHub\Modules\User\Storage\RoleTransaction;
use AnhNhan\ModHub\Modules\User\Transaction\RoleTransactionEditor;
use AnhNhan\ModHub\Modules\User\Storage\User;

use AnhNhan\ModHub\Modules\User\Query\RoleQuery;

use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;
use AnhNhan\ModHub\Storage\Types\UID;

use Symfony\Component\Yaml\Yaml;

$container = \AnhNhan\ModHub\Web\Core::loadSfDIContainer();

$userApp = new UserApplication;
$userApp->setContainer($container);
$userEm  = $userApp->getEntityManager();
$roleRepo = $userEm->getRepository('AnhNhan\ModHub\Modules\User\Storage\Role');

$defaultRolesConfigPath = mh\get_root_super() . 'resources/default.roles.yml';
$parsed = Yaml::parse($defaultRolesConfigPath);
$defaultRoles = $parsed['roles'];

echo "Using: {$defaultRolesConfigPath}\n\n";

foreach ($defaultRoles as $roleName => $roleValues) {
    $role = $roleRepo->findOneBy(array('name' => $roleName));
    if (!$role) {
        $role = new Role;

        $editor = RoleTransactionEditor::create($userEm)
            ->setActor(User::USER_UID_NONE)
            ->setEntity($role)
            ->setBehaviourOnNoEffect(RoleTransactionEditor::NO_EFFECT_SKIP)
            ->addTransaction(
                RoleTransaction::create(TransactionEntity::TYPE_CREATE, $roleName)
            )
            ->addTransaction(
                RoleTransaction::create(RoleTransaction::TYPE_EDIT_LABEL, $roleValues['label'])
            )
            ->addTransaction(
                RoleTransaction::create(RoleTransaction::TYPE_EDIT_DESC, $roleValues['description'])
            )
        ;

        $editor->apply();
        echo " [I] - Inserted '{$roleName}'\n";
    } else {
        echo " [S] - Found '{$role->name}', doing nothing\n";
    }
}

echo "\nDone.\n";
