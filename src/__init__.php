<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/AnhNhan/Converge/Modules/Symbols/SymbolLoader.php";
require_once __DIR__ . "/AnhNhan/Converge/constants.php";

use AnhNhan\Converge\Modules\Symbols\SymbolLoader;

SymbolLoader::setStaticRootDir(__DIR__);
$symbolLoader = SymbolLoader::getInstance();
$symbolLoader->register();
$symbolLoader->loadAllSymbols();
