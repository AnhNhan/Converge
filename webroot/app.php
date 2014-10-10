<?php

require_once __DIR__ . "/../src/__init__.php";

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Symbols\SymbolLoader;
use AnhNhan\Converge\Web\AppRouting;
use AnhNhan\Converge\Web\HttpKernel;

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

Debug::enable();

$container = \AnhNhan\Converge\Web\Core::loadBootstrappedSfDIContainer();
$stopwatch = $container->get('stopwatch');
$stopwatch->start('page-loadtime');

$session = $container->get('session');
$session->start();

if ($session->has('_security_token'))
{
    $token = $session->get('_security_token');
    $security_context = $container->get('security.context');
    $security_context->setToken($token);
}

$request = Request::createFromGlobals();
$request->setSession($session);
if (Converge\is_cli()) {
    Converge\sdx($argv);
    $request->server->set("REQUEST_URI", Converge\sdx($argv, "/"));
}

$router = new AppRouting($container->get('app.list'));

$request_stack = new RequestStack;
$container->set('request_stack', $request_stack);

$event_dispatcher = $container->get('event_dispatcher');

foreach (mpull($container->get('app.list')->apps(), 'getRegisteredEventListeners') as $listeners)
{
    foreach ($listeners as $listener_data)
    {
        $event_dispatcher->addListener($listener_data['event.name'], $listener_data['event.listener']);
    }
}

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
//print_r($container->get('logger.doctrine.sql')->queries);
