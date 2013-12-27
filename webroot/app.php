<?php

require_once __DIR__ . "/../src/__init__.php";

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;
use AnhNhan\ModHub\Web\AppRouting;
use AnhNhan\ModHub\Web\HttpKernel;

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

Debug::enable();

$argv = isset($argv) ? $argv : array();
ModHub\sdx($argv);
$page = ModHub\is_cli() ? ModHub\sdx($argv, "/") : $_REQUEST['page'];

$request = Request::createFromGlobals();
$request->request->add(array("page" => $page));
$request->query->add(array("page" => $page));

$classes = SymbolLoader::getInstance()
    ->getConcreteClassesThatDeriveFromThisOne('AnhNhan\ModHub\Web\Application\BaseApplication');
$router = new AppRouting($classes);

$kernel = new HttpKernel($router);
$response = $kernel->handle($request);
$response->send();
