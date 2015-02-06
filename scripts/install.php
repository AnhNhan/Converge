<?php

require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\Converge as cv;

use AnhNhan\Converge\Modules\People\PeopleApplication;
use AnhNhan\Converge\Modules\People\Storage\Role;
use AnhNhan\Converge\Modules\People\Storage\RoleTransaction;
use AnhNhan\Converge\Modules\People\Transaction\RoleTransactionEditor;
use AnhNhan\Converge\Modules\People\Storage\Email;
use AnhNhan\Converge\Modules\People\Storage\User;

use AnhNhan\Converge\Modules\People\Query\RoleQuery;
use AnhNhan\Converge\Modules\People\Query\PeopleQuery;
use AnhNhan\Converge\Modules\People\Storage\UserTransaction;
use AnhNhan\Converge\Modules\People\Transaction\UserTransactionEditor;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Types\UID;

use Symfony\Component\Yaml\Yaml;

// TODO: Consider checking that Converge really hasn't been installed yet.

$container = \AnhNhan\Converge\Web\Core::loadSfDIContainer();

$userApp = new PeopleApplication;
$userApp->setContainer($container);
$userEm  = $userApp->getEntityManager();
$roleRepo = $userEm->getRepository('AnhNhan\Converge\Modules\People\Storage\Role');

$defaultRolesConfigPath = cv\get_root_super() . 'resources/default.roles.yml';
$parsed = Yaml::parse($defaultRolesConfigPath);
$defaultRoles = $parsed['roles'];

$username = phutil_console_prompt('Please enter your username:');
$password = phutil_console_prompt('Please enter your password:');
$usermail = phutil_console_prompt('Please enter your email:');

// TODO: Check & validate data

$obj_user = new User;
$obj_email = new Email;

$pwEncoderFactory = $userApp->getService('security.encoder.factory');
$pwEncoder        = $pwEncoderFactory->getEncoder($obj_user);

// Just saving us some elaborated code
// TODO: Do this properly
$obj_email->email = $usermail;
$obj_email->user  = $obj_user;
$obj_email->is_verified = true;
$obj_email->is_primary  = true;

// Set primary email of user object
$userReflProp = $userEm->getClassMetadata(get_class($obj_user))
    ->reflClass->getProperty('primary_email');
$userReflProp->setAccessible(true);
$userReflProp->setValue(
    $obj_user, $usermail
);

$salt = User::generateSalt();
$pw      = $pwEncoder->encodePassword($password, $salt);
$xact_pw = $salt . UserTransactionEditor::SALT_PW_SEPARATOR . $pw;

$editor = UserTransactionEditor::create($userEm)
    ->setActor(User::USER_UID_NONE)
    ->setFlushBehaviour(UserTransactionEditor::FLUSH_DONT_FLUSH)
    ->setEntity($obj_user)
;

$editor
    ->addTransaction(
        UserTransaction::create(TransactionEntity::TYPE_CREATE, $username)
    )
    ->addTransaction(
        UserTransaction::create(UserTransaction::TYPE_EDIT_PASSWORD, $xact_pw)
    )
    ->addTransaction(
        UserTransaction::create(UserTransaction::TYPE_ADD_EMAIL, $usermail)
    )
;

try {
    $editor->apply();
    $userEm->persist($obj_user);
    $userEm->persist($obj_email);
    $userEm->flush();
} catch (Exception $e) {
    echo "Could not generate user.\n";
    throw $e;
}

echo "Created user {$obj_user->name} ({$obj_user->uid})\n\n";

$role_user = null;

echo "Inserting default user roles, using: {$defaultRolesConfigPath}\n\n";

foreach ($defaultRoles as $roleName => $roleValues) {
    $role = $roleRepo->findOneBy(array('name' => $roleName));
    if (!$role) {
        $role = new Role;

        $editor = RoleTransactionEditor::create($userEm)
            ->setActor($obj_user->uid)
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

        if ($roleName == 'ROLE_USER')
        {
            $role_user = $role;
        }

        $editor->apply();
        echo " [I] - Inserted '{$roleName}'\n";
    } else {
        echo " [S] - Found '{$role->name}', doing nothing\n";
    }
}

echo "Done with roles.\n\n";

UserTransactionEditor::create($userEm)
    ->setActor(User::USER_UID_NONE)
    ->setFlushBehaviour(UserTransactionEditor::FLUSH_DONT_FLUSH)
    ->setEntity($obj_user)
    ->addTransaction(
        UserTransaction::create(UserTransaction::TYPE_ADD_ROLE, $role_user->uid)
    )
    ->apply()
;
$userEm->flush();

echo "Congratulations, you are now a ROLE_USER!\n";
echo "We are done, btw.";
