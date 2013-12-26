<?php

require_once __DIR__ . "/../src/__init__.php";

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\Page\DefaultTemplateView;
use AnhNhan\ModHub\Web\Core;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;

use Symfony\Component\Debug\Debug;

use Symfony\Component\HttpFoundation\Request;

Debug::enable();

// TODO: Put this somewhere reasonable
ResMgr::init(ModHub\path("__resource_map__.php"));
ResMgr::getInstance()
    ->requireCSS("core-pck")
    ->requireJS("libs-pck");

$argv = isset($argv) ? $argv : array();
ModHub\sdx($argv);
$page = ModHub\is_cli() ? ModHub\sdx($argv, "/") : $_REQUEST['page'];

$core = new Core;
$response = $core->handlePage($page);
$response->send();
