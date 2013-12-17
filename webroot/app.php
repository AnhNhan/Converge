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

$form = new FormView();
$form->enableFileUpload()
    ->setAction("user/login");
$form->append(id(new TextControl())
    ->setLabel('Reason for your coming')
    ->setValue('Peace'));

$textControl = new TextControl();
$textControl->setName('username')
    ->setValue('Your name')
    ->setLabel('This is the label for your name');
$form->append($textControl);

$form->append(id(new TextControl())
    ->setLabel('Name of your mom')
    ->setValue('Dorothy'));
$form->append(id(new TextAreaControl())
    ->setValue("This is some text")
    ->setLabel("Description"));
$form->append(id(new SubmitControl())
    ->addCancelButton('/')
    ->addSubmitButton('Hasta la vista!'));
$container->push(ModHub\ht("div", $form->render()->addClass("width12"))->addClass("row-flex"));

$instance = new \AnhNhan\ModHub\Views\Page\DefaultTemplateView("title", $container);

echo $instance->render();
}
