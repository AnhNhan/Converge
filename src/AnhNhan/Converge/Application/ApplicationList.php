<?php
namespace AnhNhan\Converge\Application;

use AnhNhan\Converge\Modules\Symbols\SymbolLoader;
use AnhNhan\Converge\Web\Application\BaseApplication;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ApplicationList
{
    /**
     * @var ContainerInterface
     */
    private $container;

    const APPLICATION_BASE_CLASS = 'AnhNhan\Converge\Web\Application\BaseApplication';

    private $application_base_class;

    private $app_classes = array();
    private $apps = array();
    private $active_apps = array();

    public function __construct(ContainerInterface $container, $base_class = self::APPLICATION_BASE_CLASS)
    {
        $this->container = $container;
        $this->application_base_class = $base_class;
        $this->app_classes = SymbolLoader::getInstance()
            ->getConcreteClassesThatDeriveFromThisOne($this->application_base_class)
        ;
        foreach ($this->app_classes as $class) {
            $app = new $class;
            $app->setContainer($this->container);
            $this->apps[$app->getInternalName()] = $app;
        }

        $this->active_apps = mfilter($this->apps, 'isApplicationEnabled');
    }

    public function apps()
    {
        return $this->active_apps;
    }

    public function allApps()
    {
        return $this->apps;
    }

    public function app($name)
    {
        if (!isset($this->apps[$name])) {
            throw new \Exception("App '$name' does not exist!");
        }
        return $this->apps[$name];
    }

    public function classes()
    {
        return $this->app_classes;
    }

    public function classFor($name)
    {
        if (!isset($this->app_classes[$name])) {
            throw new \Exeption("App '$name' does not exist!");
        }
        return $this->app_classes[$name];
    }
}
