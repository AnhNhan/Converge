<?php

require_once __DIR__ . "/../vendor/autoload.php";

$symbolLoader = SymbolLoader::getInstance();
$symbolLoader->register();
$symbolLoader->loadAllFunctions();
