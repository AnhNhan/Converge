<?php

$forumApp = new \AnhNhan\Converge\Modules\Forum\ForumApplication;
$forumApp->setContainer(\AnhNhan\Converge\Web\Core::loadBootstrappedSfDIContainer());
\Codeception\Module\Doctrine2::$em = $forumApp->getEntityManager();
