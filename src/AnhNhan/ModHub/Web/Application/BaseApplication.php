<?php
namespace AnhNhan\ModHub\Web\Application;

use AnhNhan\ModHub;
use YamwLibs\Libs\Http\Request;
use YamwLibs\Libs\Routing\Route;

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
    protected function getDatabaseConfigForDoctrine()
    {
        $dbPath = ModHub\get_root_super() . "cache/db/" . $this->getInternalName() . ".sqlite";
        return array(
            'driver' => 'pdo_sqlite',
            'path' => $dbPath,
        );
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        static $em;
        if (!$em) {
            $em = $this->buildEntityManager();
            if (!\Doctrine\DBAL\Types\Type::hasType("uid")) {
                \Doctrine\DBAL\Types\Type::addType("uid", 'AnhNhan\ModHub\Storage\UIDType');
            }
            $conn = $em->getConnection();
            $platform = $conn->getDatabasePlatform();
            $platform->registerDoctrineTypeMapping("varchar", \Doctrine\DBAL\Types\Type::getType("uid")->getName());
        }

        return $em;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function buildEntityManager()
    {
        throw new \Exception("This application does not have an entity manager!");
    }
}
