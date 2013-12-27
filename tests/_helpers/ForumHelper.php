<?php
namespace Codeception\Module;

use AnhNhan\ModHub\Storage\Types\UID;

class ForumHelper extends \Codeception\Module
{
    public function getApplication()
    {
        static $app;
        if (!$app) {
            $app = new \AnhNhan\ModHub\Modules\Forum\ForumApplication;
            $app->setContainer(\AnhNhan\ModHub\Web\Core::loadSfDIContainer());
        }
        return $app;
    }

    public function getEntityManager()
    {
        static $em;
        if (!$em) {
            $em = $this->getApplication()->getEntityManager();
        }
        return $em;
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
