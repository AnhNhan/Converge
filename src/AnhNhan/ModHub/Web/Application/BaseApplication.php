<?php
namespace AnhNhan\ModHub\Web\Application;

use AnhNhan\ModHub;
use YamwLibs\Libs\Http\Request;
use YamwLibs\Libs\Routing\Route;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class BaseApplication
{
    /**
     * Empty constructor
     */
    final public function __construct()
    {
        // Empty
    }

    abstract public function getHumanReadableName();
    abstract public function getInternalName();

    abstract public function getRoutes();

    abstract public function routeToController(Request $request);

    protected function generateRoutesFromYaml($file)
    {
        $yaml = \Symfony\Component\Yaml\Yaml::parse($file);
        $routes = $yaml["routes"];

        $routeObjects = array();
        $ii = 0;
        foreach ($routes as $blob) {
            $route = new Route(idx($blob, "route-name", "route-$file-$ii"), $blob["pattern"], $this);

            $reqs = idx($blob, "requirements");
            $params = idx($blob, "parameters");

            if ($reqs) {
                $route->setRequirements($reqs);
            }
            if ($params) {
                $route->setParameters($params);
            }

            $routeObjects[] = $route;
            $ii++;
        }

        return $routeObjects;
    }

    /**
     * Dummy until we get containers going
     *
     * @return array
     */
    protected function getDatabaseConfigForDoctrine($dbName)
    {
        $dbPath = ModHub\get_root_super() . "cache/db/" . $dbName . ".sqlite";
        return array(
            'driver' => 'pdo_sqlite',
            'path' => $dbPath,
        );
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager($dbName = null)
    {
        static $em = array();
        if (!isset($em[$dbName])) {
            $em = $this->buildEntityManager($this->getDatabaseConfigForDoctrine($dbName ?: $this->getInternalName()));
        }

        return $em;
    }

    protected function buildEntityManager($dbConfig)
    {
        throw new \Exception("This application does not have an entity manager!");
    }

    protected function buildDefaultEntityManager($dbConfig, array $paths)
    {
        $isDevMode = true;
        // $proxyDir = ModHub\get_root_super() . "cache/proxies/";
        $proxyDir = sys_get_temp_dir();

        // TODO: Make this less static
        $cache = new \Doctrine\Common\Cache\MongoDBCache(id(new \MongoClient)->selectCollection("modhub", "dc2"));
        $cache->setNamespace("dc2_" . md5($proxyDir) . "_"); // to avoid collisions

        $config = new Configuration();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setResultCacheImpl($cache);
        $config->setProxyDir($proxyDir);
        $config->setProxyNamespace('DoctrineProxies');
        $config->setAutoGenerateProxyClasses($isDevMode);

        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver($paths, true));

        return EntityManager::create($dbConfig, $config);
    }
}
