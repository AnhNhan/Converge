<?php

require_once __DIR__ . "/../vendor/autoload.php";

use YamwLibs\Infrastructure\Symbols\SymbolLoader;

$symbolLoader = new SymbolLoader(__DIR__);
$symbolLoader->register();
$symbolLoader->loadAllFunctions();
