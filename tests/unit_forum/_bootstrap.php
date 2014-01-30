<?php

$forumApp = new \AnhNhan\ModHub\Modules\Forum\ForumApplication;
$forumApp->setContainer(\AnhNhan\ModHub\Web\Core::loadBootstrappedSfDIContainer());
\Codeception\Module\Doctrine2::$em = $forumApp->getEntityManager();
