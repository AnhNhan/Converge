<?php
namespace AnhNhan\ModHub;

require_once __DIR__ . "/../../__init__.php";

use Symfony\Component\Console\Application;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;

$container = \AnhNhan\ModHub\Web\Core::loadSfDIContainer();

$command_classes = SymbolLoader::getInstance()
    ->getConcreteClassesThatDeriveFromThisOne('AnhNhan\ModHub\Console\ConsoleCommand');

$cli_application = new Application("ModHub CLI Interface Manager", "0.0.0.0.1");

foreach ($command_classes as $class_name) {
    $cli_application->add(id(new $class_name)->setContainer($container));
}

$cli_application->run();
