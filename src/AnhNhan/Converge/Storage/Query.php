<?php
namespace AnhNhan\Converge\Storage;

use AnhNhan\Converge\Web\Application\BaseApplication;
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
        } else if ($appOrEm instanceof EntityManager) {
            $this->em = $appOrEm;
        } else {
            throw new \InvalidArgumentException("Argument has to be EntityManager object or a BaseApplication.");
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

    private $external_queries = array();

    const EXT_QUERY_FORUM = 'forum';
    const EXT_QUERY_ROLE  = 'role';
    const EXT_QUERY_TAG   = 'tag';
    const EXT_QUERY_USER  = 'user';

    final public function addExternalQueryFromApplication($name, BaseApplication $app)
    {
        return $this->addExternalQueryFromEntityManager($name, $app->getEntityManager());
    }

    final public function addExternalQueryFromEntityManager($name, EntityManager $em)
    {
        if (isset($this->external_queries[$name])) {
            throw new \RunTimeException("Query '{$name}' already added.");
        }

        $query_class = '\\' . $this->getQueryClassFor($name);
        $query = new $query_class($em);
        $this->external_queries[$name] = $query;

        return $this;
    }

    // TODO: Generate this or make this guessable
    private function getQueryClassFor($name)
    {
        return idx(array(
            self::EXT_QUERY_FORUM => 'AnhNhan\Converge\Modules\Forum\Query\DiscussionQuery',
            self::EXT_QUERY_ROLE  => 'AnhNhan\Converge\Modules\People\Query\RoleQuery',
            self::EXT_QUERY_TAG   => 'AnhNhan\Converge\Modules\Tag\TagQuery',
            self::EXT_QUERY_USER  => 'AnhNhan\Converge\Modules\People\Query\UserQuery',
        ), $name);
    }

    final protected function requireExternalQuery($name)
    {
        if (!isset($this->external_queries[$name])) {
            throw new \Exception("Query '{$name}' is not loaded!");
        }

        return $this->external_queries[$name];
    }
}
