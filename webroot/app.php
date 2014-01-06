<?php

require_once __DIR__ . "/../src/__init__.php";

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;
use AnhNhan\ModHub\Web\AppRouting;
use AnhNhan\ModHub\Web\HttpKernel;

use Symfony\Component\Debug\Debug;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

Debug::enable();

$request = Request::createFromGlobals();
if (ModHub\is_cli()) {
    ModHub\sdx($argv);
    $request->server->set("REQUEST_URI", ModHub\sdx($argv, "/"));
}

$classes = SymbolLoader::getInstance()
    ->getConcreteClassesThatDeriveFromThisOne('AnhNhan\ModHub\Web\Application\BaseApplication');
$router = new AppRouting($classes);

$container = \AnhNhan\ModHub\Web\Core::loadSfDIContainer();

$eventDispatcher = new ContainerAwareEventDispatcher($container);
$container->set('event_dispatcher', $eventDispatcher);

$request_stack = new RequestStack;
$container->set('request_stack', $request_stack);

$kernel = new HttpKernel($eventDispatcher, $router, $request_stack);
$kernel->setContainer($container);
$container->set('http_kernel', $kernel);
$response = $kernel->handle($request);
$response->send();
