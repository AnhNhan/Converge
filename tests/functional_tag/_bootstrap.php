<?php

$tagApp = new \AnhNhan\ModHub\Modules\Tag\TagApplication;
$tagApp->setContainer(\AnhNhan\ModHub\Web\Core::loadSfDIContainer());
\Codeception\Module\Doctrine2::$em = $tagApp->getEntityManager();
