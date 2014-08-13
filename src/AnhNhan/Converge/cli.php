<?php
namespace AnhNhan\Converge;

require_once __DIR__ . "/../../__init__.php";

use Symfony\Component\Console\Application;
use AnhNhan\Converge\Modules\Symbols\SymbolLoader;

$container = \AnhNhan\Converge\Web\Core::loadSfDIContainer();

$command_classes = SymbolLoader::getInstance()
    ->getConcreteClassesThatDeriveFromThisOne('AnhNhan\Converge\Console\ConsoleCommand');

$cli_application = new Application("Converge CLI Interface Manager", "0.0.0.0.1");

foreach ($command_classes as $class_name) {
    $cli_application->add(id(new $class_name)->setContainer($container));
}

$cli_application->run();
