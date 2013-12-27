<?php

$userApp = new \AnhNhan\ModHub\Modules\User\UserApplication;
$userApp->setContainer(\AnhNhan\ModHub\Web\Core::loadSfDIContainer());
\Codeception\Module\Doctrine2::$em = $userApp->getEntityManager();
