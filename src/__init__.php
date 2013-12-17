<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/AnhNhan/ModHub/Modules/Symbols/SymbolLoader.php";

use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;

SymbolLoader::setStaticRootDir(__DIR__);
$symbolLoader = SymbolLoader::getInstance();
$symbolLoader->register();
$symbolLoader->loadAllFunctions();
