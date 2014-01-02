<?php
namespace AnhNhan\ModHub\Storage;

use AnhNhan\ModHub\Web\Application\BaseApplication;
use Doctrine\ORM\EntityManager;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class Query
{
    /**
     * @var EntityManager
     */
    private $em;

    final public function __construct($appOrEm)
    {
        if ($appOrEm instanceof BaseApplication) {
            $this->em = $appOrEm->getEntityManager();
        } else {
            $this->em = $appOrEm;
        }
    }

    final protected function em()
    {
        return $this->em;
    }

    final protected function repository($entityName)
    {
        return $this->em()->getRepository($entityName);
    }
}
