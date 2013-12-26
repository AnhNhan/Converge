<?php

require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\ModHub;
use AnhNhan\ModHub\Web\Core;
use Symfony\Component\Stopwatch\Stopwatch;

$core = new Core;
$stopWatch = new Stopwatch;

$buildEventWatch = $stopWatch->start("build");
$container = Core::buildSfDIContainer();
$buildEventWatch->stop();

unlink(ModHub\get_root_super() . "cache/container.default.php");

$loadEventWatch = $stopWatch->start("load");
$container = Core::loadSfDIContainer();
$loadEventWatch->stop();

$loadEvent2Watch = $stopWatch->start("load2");
$container = Core::loadSfDIContainer();
$loadEvent2Watch->stop();

ModHub\println("Fresh build event:\t" . $buildEventWatch->getDuration() . "ms");
ModHub\println("Fresh load event:\t" . $loadEventWatch->getDuration() . "ms");
ModHub\println("Cache load event:\t" . $loadEvent2Watch->getDuration() . "ms");
