<?php

require_once __DIR__ . "/../src/__init__.php";

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumListing;
use AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumObject;
use AnhNhan\ModHub\Modules\Tag\Views\TagView;
use AnhNhan\ModHub\Views\Form\FormView;
use AnhNhan\ModHub\Views\Form\Controls\SubmitControl;
use AnhNhan\ModHub\Views\Form\Controls\TextControl;
use AnhNhan\ModHub\Views\Form\Controls\TextAreaControl;
use AnhNhan\ModHub\Views\Objects;
use AnhNhan\ModHub\Web\Core;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

ob_start();

// TODO: Put this somewhere reasonable
ResMgr::init(ModHub\path("__resource_map__.php"));
ResMgr::getInstance()
    ->requireCSS("core-pck");

$argv = isset($argv) ? $argv : array();
ModHub\sdx($argv);
$page = ModHub\is_cli() ? ModHub\sdx($argv, "/") : $_REQUEST['page'];

$core = new Core;
$request = $core->init($page);

$controller = $core->dispatchRequest($request);
// TODO: Handle more processing here

if ($controller) {
$payload = $controller->setRequest($request)->handle();
$renderedPayload = $payload->render();

$overflow = ob_get_clean();

if ($overflow) {
    echo "<div style=\"text-align: left; margin: 1em;\">";
    echo "<h3>We had overflow!</h3>";
    echo "<pre>" . (new \YamwLibs\Libs\Html\Markup\TextNode($overflow)) . "</pre>";
    echo "</div>";
}

echo $renderedPayload;

// TODO: Put this in some demo application
} else {

$container = new MarkupContainer;

$instance = new \AnhNhan\ModHub\Views\Page\DefaultTemplateView("title", $container);

echo $instance->render();
}
