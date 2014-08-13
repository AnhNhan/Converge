<?php

$userApp = new \AnhNhan\Converge\Modules\User\UserApplication;
$userApp->setContainer(\AnhNhan\Converge\Web\Core::loadBootstrappedSfDIContainer());
\Codeception\Module\Doctrine2::$em = $userApp->getEntityManager();
