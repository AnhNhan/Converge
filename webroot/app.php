<?php

require_once __DIR__ . "/../src/__init__.php";

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;
use AnhNhan\ModHub\Web\AppRouting;
use AnhNhan\ModHub\Web\HttpKernel;

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

Debug::enable();

$container = \AnhNhan\ModHub\Web\Core::loadBootstrappedSfDIContainer();
$stopwatch = $container->get('stopwatch');
$stopwatch->start('page-loadtime');

$request = Request::createFromGlobals();
if (ModHub\is_cli()) {
    ModHub\sdx($argv);
    $request->server->set("REQUEST_URI", ModHub\sdx($argv, "/"));
}

$router = new AppRouting($container->get('app.list'));

$request_stack = new RequestStack;
$container->set('request_stack', $request_stack);

$event_dispatcher = $container->get('event_dispatcher');

$kernel = new HttpKernel($event_dispatcher, $router, $request_stack);
$kernel->setContainer($container);
$container->set('http_kernel', $kernel);
$response = $kernel->handle($request);

$contents = $response->getContent();
$contents = str_replace('{{time}}', $container->get('stopwatch')->stop('page-loadtime')->getDuration() . 'ms', $contents);
$contents = str_replace('{{queries}}', $container->get('logger.doctrine.sql')->currentQuery, $contents);
$contents = str_replace('{{memory}}', round(memory_get_peak_usage() / 1024 / 1024, 2), $contents);
$response->setContent($contents);

$response->send();
