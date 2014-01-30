<?php

$tagApp = new \AnhNhan\ModHub\Modules\Tag\TagApplication;
$tagApp->setContainer(\AnhNhan\ModHub\Web\Core::loadBootstrappedSfDIContainer());
\Codeception\Module\Doctrine2::$em = $tagApp->getEntityManager();
