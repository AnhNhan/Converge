<?php
namespace AnhNhan\ModHub\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Used by SymbolLoader to trace all console commands contained in this application.
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class ConsoleCommand extends Command
{
    /**
     * For certain internal purposes only. Do not use unless you know what you
     * are doing.
     *
     * Retrieves an external application, which will share the same service
     * container as this console command.
     *
     * @return BaseApplication The requested application
     *
     * @throws Exception If the app could not be found
     */
    final protected function getExternalApplication($internalName)
    {
        // Hm, should we replace this with an app list from the container?
        $classes = SymbolLoader::getInstance()
            ->getConcreteClassesThatDeriveFromThisOne('AnhNhan\ModHub\Web\Application\BaseApplication');
        $apps = array();
        foreach ($classes as $class_name) {
            $apps[] = new $class_name;
        }
        $apps = mpull($apps, null, "getInternalName");
        $app = idx($apps, $internalName);
        if (!$app) {
            throw new \Exception("App '{$internalName}' does not exist!");
        }
        $app->setContainer($this->container);
        return $app;
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
            return null;
        }
        return $this->container->get($service, ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }

    final public function getServiceParameter($parameter)
    {
        if (!$this->container) {
            return null;
        }
        return $this->container->getParameter($parameter);
    }
}
