<?php

require_once __DIR__ . "/../vendor/autoload.php";

use YamwLibs\Infrastructure\Symbols\SymbolLoader;

SymbolLoader::setStaticRootDir(__DIR__);
$symbolLoader = SymbolLoader::getInstance();
$symbolLoader->register();
$symbolLoader->loadAllFunctions();
