<?php

require_once __DIR__ . "/../src/__init__.php";

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;
use AnhNhan\ModHub\Web\AppRouting;
use AnhNhan\ModHub\Web\HttpKernel;

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

Debug::enable();

$request = Request::createFromGlobals();
if (ModHub\is_cli()) {
    ModHub\sdx($argv);
    $request->server->set("REQUEST_URI", ModHub\sdx($argv, "/"));
}

$classes = SymbolLoader::getInstance()
    ->getConcreteClassesThatDeriveFromThisOne('AnhNhan\ModHub\Web\Application\BaseApplication');
$router = new AppRouting($classes);

$kernel = new HttpKernel($router);
$response = $kernel->handle($request);
$response->send();
