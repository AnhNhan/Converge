<?php

$userApp = new \AnhNhan\ModHub\Modules\User\UserApplication;
$userApp->setContainer(\AnhNhan\ModHub\Web\Core::loadBootstrappedSfDIContainer());
\Codeception\Module\Doctrine2::$em = $userApp->getEntityManager();
