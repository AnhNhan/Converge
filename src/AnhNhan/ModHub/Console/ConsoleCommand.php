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
        return $this->container->get($service, ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }

    final public function getServiceParameter($parameter)
    {
        return $this->container->getParameter($parameter);
    }
}
