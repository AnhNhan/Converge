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

$overflow = ob_get_clean();

if ($overflow) {
    echo "<h3>We had overflow!</h3>";
    echo "<pre>" . (new \YamwLibs\Libs\Html\Markup\TextNode($overflow)) . "</pre>";
}

if ($controller) {
// TODO: Put this above ob_get_clean after we have implemented Response
$payload = $controller->setRequest($request)->handle();

// TODO: Put this in some demo application
} else {

$container = new MarkupContainer;

$listing = new ForumListing;
$listing->setTitle('Forum Listing');

$listing->addObject(
    id(new ForumObject)
        ->setHeadline('A little story of the future')
        ->addTag(new TagView("caw", "green"))
        ->addTag(new TagView("internal"))
);
$listing->addObject(
    id(new ForumObject)
        ->setHeadline('Why the future is the future')
        ->addTag(new TagView("caw", "green"))
        ->addTag(new TagView("sotp", "blue"))
        ->addTag(new TagView("homefront", "dark"))
        ->addTag(new TagView("discussion"))
);
$listing->addObject(
    id(new ForumObject)
        ->setHeadline('Future, I am your father')
        ->addTag(new TagView("caw", "green"))
        ->addTag(new TagView("sotp", "blue"))
);
$row = ModHub\ht("div")->addClass("row");
$row->appendContent($listing->render()->addClass("width12"))->appendContent($listing->render()->addClass("width6"))->appendContent($listing->render()->addClass("width6"))->appendContent($listing->render()->addClass("width12"));
$container->push($row);

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
