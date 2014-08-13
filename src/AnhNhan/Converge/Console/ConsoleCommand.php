<?php
namespace AnhNhan\Converge\Console;

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
        return $this->container->get('app.list')->app($name);
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
            throw new \Exception("This command has no service container!");
        }
        return $this->container->get($service, ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }

    final public function getServiceParameter($parameter)
    {
        if (!$this->container) {
            throw new \Exception("This command has no service container!");
        }
        return $this->container->getParameter($parameter);
    }
}
