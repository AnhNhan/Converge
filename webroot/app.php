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
use AnhNhan\ModHub\Views\Page\DefaultTemplateView;
use AnhNhan\ModHub\Web\Core;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\Debug\Debug;

Debug::enable();

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
} else {
    ob_start();
    echo "<h2>Failed to find a controller for '$page'</h2>";
    echo "<pre>";
    print_r($core);
    echo "</pre>";
    $contents = ModHub\safeHtml(ob_get_clean());
    $view = new DefaultTemplateView("Routing error", $contents);
    echo $view;
    exit(1);
}
$renderedPayload = $payload->render();

$overflow = ob_get_clean();

if ($overflow) {
    echo "<div style=\"text-align: left; margin: 1em;\">";
    echo "<h3>We had overflow!</h3>";
    echo "<pre>" . (new \YamwLibs\Libs\Html\Markup\TextNode($overflow)) . "</pre>";
    echo "</div>";
}

echo $renderedPayload;

