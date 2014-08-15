<?php

require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\Converge as cv;

use AnhNhan\Converge\Modules\User\UserApplication;
use AnhNhan\Converge\Modules\User\Storage\Role;
use AnhNhan\Converge\Modules\User\Storage\RoleTransaction;
use AnhNhan\Converge\Modules\User\Transaction\RoleTransactionEditor;
use AnhNhan\Converge\Modules\User\Storage\User;

use AnhNhan\Converge\Modules\User\Query\RoleQuery;
use AnhNhan\Converge\Modules\User\Query\UserQuery;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Types\UID;

use Symfony\Component\Yaml\Yaml;

$container = \AnhNhan\Converge\Web\Core::loadSfDIContainer();

$userApp = new UserApplication;
$userApp->setContainer($container);
$userEm  = $userApp->getEntityManager();
$roleRepo = $userEm->getRepository('AnhNhan\Converge\Modules\User\Storage\Role');

$defaultRolesConfigPath = cv\get_root_super() . 'resources/default.roles.yml';
$parsed = Yaml::parse($defaultRolesConfigPath);
$defaultRoles = $parsed['roles'];

$userQuery = new UserQuery($userApp);
$anh_nhan  = $userQuery->retrieveUsersForCanonicalNames(['anhnhan']);
$anh_nhan  = idx($anh_nhan, 0);
if (!$anh_nhan)
{
    throw new Exception('User Anh Nhan does not exist.');
}

echo "Using: {$defaultRolesConfigPath}\n\n";

foreach ($defaultRoles as $roleName => $roleValues) {
    $role = $roleRepo->findOneBy(array('name' => $roleName));
    if (!$role) {
        $role = new Role;

        $editor = RoleTransactionEditor::create($userEm)
            ->setActor($anh_nhan->uid)
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
