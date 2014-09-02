<?php
namespace AnhNhan\Converge\Web\Application;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Symbols\SymbolLoader;
use YamwLibs\Libs\Routing\Route;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

use Filesystem;
use FilesystemException;

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

    /**
     * Routes an incoming request to a controller of this application.
     *
     * @param  Request $request A Request object.
     *
     * @return BaseApplicationConstroller|null
     *             Either an instance of BaseApplicationController, then take that
     *             as a controller.
     *             Can also be null, in that case we'll try to resolve the controller
     *             from other sources, in the worst case we'll throw back the 404 controller.
     */
    public function routeToController(Request $request)
    {
        return null;
    }

    protected function generateRoutesFromYaml($file)
    {
        $filename_hash = md5($file);
        $cache_dir = Converge\get_root_super() . "cache/";
        $cache_file = $cache_dir . $filename_hash . ".php";
        $cache_meta = $cache_file . ".meta";

        $generate = function () use ($file, $cache_meta, $cache_file) {
            $routes = $this->generateRoutesFromYamlReal($file);
            file_put_contents($cache_file, serialize($routes));
            file_put_contents($cache_meta, filemtime($file));
            return $routes;
        };

        $routes = array();

        if (!file_exists($cache_meta)) {
            $routes = $generate();
        }

        if (!$routes && $cache_mtime = (int) @file_get_contents($cache_meta)) {
            if (filemtime($file) > $cache_mtime) {
                $routes = $generate();
            }

            if (!$routes) {
                $routes = unserialize(Filesystem::readFile($cache_file));
            }
        }

        return $routes;
    }

    private function generateRoutesFromYamlReal($file)
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

    public function getCustomMarkupRules()
    {
        return [];
    }

    /**
     * Return something like
     * [
     *     'UIDX-TYPE' => $this->createActivityRenderer(...),
     *     ...
     * ]
     */
    public function getActivityRenderers()
    {
        return [];
    }

    protected static function createActivityRenderer(callable $label_fun, callable $body_fun = null, callable $class_fun = null, callable $external_uids_fun = null)
    {
        return [
            'label' => $label_fun,
            'body' => $body_fun,
            'class' => $class_fun,
            'external_uids' => $external_uids_fun,
        ];
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
                $dbPath = Converge\get_root_super() . "cache/db/" . $dbName . ".sqlite";
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
        $proxyDir = $this->getServiceParameter("doctrine.proxy.path") ?: sys_get_temp_dir();

        $cache = $this->getService("cache.doctrine") ?: new \Doctrine\Common\Cache\ArrayCache;
        $cache->setNamespace("dc2_" . md5($proxyDir) . "_"); // to avoid collisions

        $config = new Configuration();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setResultCacheImpl($cache);
        $config->setProxyDir($proxyDir);
        $config->setProxyNamespace('DoctrineProxies');
        $config->setAutoGenerateProxyClasses($isDevMode);
        $config->setSQLLogger($this->getService('logger.doctrine.sql'));

        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver($paths, true));

        /*$config->setSecondLevelCacheEnabled();
        $config->getSecondLevelCacheConfiguration()
            ->setCacheFactory($this->getService("doctrine.cache.region.factory"));*/

        $eventManager = new \Doctrine\Common\EventManager;
        $eventManager->addEventSubscriber(new \AnhNhan\Converge\Storage\Doctrine\LifeCycleUIDGenerator);
        $eventManager->addEventSubscriber(new \AnhNhan\Converge\Modules\Task\Doctrine\LifeCycleTaskRelationXActSerializer);

        self::initialize_doctrine_types();
        $entityManager = EntityManager::create($dbConfig, $config, $eventManager);
        $entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('json_object_array', 'json_object_array');
        return $entityManager;
    }

    private static $initialized_doctrine_types = false;

    private static function initialize_doctrine_types()
    {
        if (!self::$initialized_doctrine_types)
        {
            Type::addType('json_object_array', 'AnhNhan\Converge\Storage\Doctrine\JSONSerializedType');

            self::$initialized_doctrine_types = true;
        }
    }

    /**
     * For certain internal purposes only. Do not use unless you know what you
     * are doing.
     *
     * Retrieves an external application, which will share the same service
     * container as this application.
     *
     * @return BaseApplication The requested application
     *
     * @throws Exception If the app could not be found
     */
    final protected function getExternalApplication($internalName)
    {
        return $this->getService('app.list')->app($name);
    }

    /**
     * @var ContainerInterface
     */
    private $container;

    final public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return object|null The requested service or null if it does not exist (we
     *                     ignore exceptions)
     */
    final public function getService($service)
    {
        if (!$this->container) {
            throw new \Exception("This application has no service container!");
        }
        return $this->container->get($service, ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }

    final public function getServiceParameter($parameter)
    {
        if (!$this->container) {
            throw new \Exception("This application has no service container!");
        }
        return $this->container->getParameter($parameter);
    }
}
