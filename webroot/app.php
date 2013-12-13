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
use YamwLibs\Libs\Html\Markup\MarkupContainer;

ob_start();

$core = new Core;
$core->init($_REQUEST['page']);

// TODO: Actually to all the processing here

$overflow = ob_get_clean();

if ($overflow) {
    echo "<p>We had overflow!</p>";
    echo "<p>" . (new \YamwLibs\Libs\Html\Markup\TextNode($overflow)) . "</p>";
    var_dump($overflow);
}

$container = new MarkupContainer;

$listing = new ForumListing;
$listing->setTitle('Forum Listing');

$listing->addObject(
    id(new ForumObject)
        ->setHeadline('A little story of the future')
        ->addAttribute('Christian Müller')
        ->addAttribute('Yesterday')
        ->addAttribute('Don\'t believe me? ¬.¬')
        ->addTag(new TagView("caw", "green"))
        ->addTag(new TagView("internal"))
);
$listing->addObject(
    id(new ForumObject)
        ->setHeadline('Why the future is the future')
        ->addAttribute('Christoph Müller')
        ->addAttribute('Two days before')
        ->addTag(new TagView("caw", "green"))
        ->addTag(new TagView("sotp", "blue"))
        ->addTag(new TagView("homefront", "dark"))
        ->addTag(new TagView("discussion"))
);
$listing->addObject(
    id(new ForumObject)
        ->setHeadline('Future, I am your father')
        ->addAttribute('Hans Müller')
        ->addAttribute('Two days before')
        ->addTag(new TagView("caw", "green"))
        ->addTag(new TagView("sotp", "blue"))
);
$container->push($listing->render());

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
$container->push($form->render());

$instance = new \AnhNhan\ModHub\Views\Page\DefaultTemplateView("title", $container);

echo $instance->render();
