<?php

$tagApp = new \AnhNhan\Converge\Modules\Tag\TagApplication;
$tagApp->setContainer(\AnhNhan\Converge\Web\Core::loadBootstrappedSfDIContainer());
\Codeception\Module\Doctrine2::$em = $tagApp->getEntityManager();
