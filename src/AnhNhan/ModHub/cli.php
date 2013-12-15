<?php
namespace AnhNhan\ModHub;

require_once __DIR__ . "/../../__init__.php";

use Symfony\Component\Console\Application;
use YamwLibs\Infrastructure\Symbols\SymbolLoader;

$command_classes = SymbolLoader::getInstance()
    ->getClassesThatDeriveFromThisOne('AnhNhan\ModHub\Console\ConsoleCommand');

$cli_application = new Application("ModHub CLI Interface Manager", "0.0.0.0.1");

foreach ($command_classes as $class_name) {
    $cli_application->add(new $class_name);
}

$cli_application->run();
