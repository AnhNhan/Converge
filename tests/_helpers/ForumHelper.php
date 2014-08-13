<?php
namespace Codeception\Module;

use AnhNhan\Converge\Storage\Types\UID;

class ForumHelper extends \Codeception\Module
{
    public function getApplication()
    {
        static $app;
        if (!$app) {
            $app = new \AnhNhan\Converge\Modules\Forum\ForumApplication;
            $app->setContainer(\AnhNhan\Converge\Web\Core::loadSfDIContainer());
        }
        return $app;
    }

    public function getEntityManager()
    {
        return \Codeception\Module\Doctrine2::$em;
    }

    public function getRepository($entity)
    {
        return $this->getEntityManager()->getRepository($entity);
    }

    public function generateAuthorId()
    {
        return UID::generate("USER");
    }

    public function generateTagId()
    {
        return UID::generate("TTAG");
    }
}
