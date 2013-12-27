<?php
namespace AnhNhan\ModHub\Web\Application;

use AnhNhan\ModHub;
use YamwLibs\Libs\Routing\Route;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

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
    protected function getDatabaseConfigForDoctrine($dbName, $dbType = "mysql")
    {
        switch ($dbType) {
            case "sqlite":
                $dbPath = ModHub\get_root_super() . "cache/db/" . $dbName . ".sqlite";
                return array(
                    'driver' => 'pdo_sqlite',
                    'path' => $dbPath,
                );
                break;
            case "mysql":
                $driver = "pdo_mysql";
                return array(
                    "driver"   => $driver,
                    "host"     => "127.0.0.1",
                    "user"     => "modhub",
                    "password" => "",
                    "dbname"   => "modhub_" . $dbName,
                );
                break;
            default:
                throw new \Exception("DB Type $dbType not supported yet!");
                break;
        }
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager($dbName = null, $dbType = "mysql")
    {
        static $em = array();
        $index = $dbName . $dbType;
        if (!isset($em[$index])) {
            $em[$index] = $this->buildEntityManager($this->getDatabaseConfigForDoctrine($dbName ?: $this->getInternalName(), $dbType));
        }

        return $em[$index];
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
        //$cache = new \Doctrine\Common\Cache\MongoDBCache(id(new \MongoClient)->selectCollection("modhub", "dc2"));
        $cache = new \Doctrine\Common\Cache\ArrayCache;
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

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return object|null The requested service or null if it does not exist (we
     *                     ignore exceptions)
     */
    public function getService($service)
    {
        return $this->container->get($service, ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }
}
