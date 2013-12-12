<?php

require_once __DIR__ . "/../src/__init__.php";

use AnhNhan\ModHub;
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

$content = "<p>This is supposed to be the content</p>";

$overflow = ob_get_clean();

// echo $content;

if ($overflow) {
    echo "<p>We had overflow!</p>";
    echo "<p>" . (new \YamwLibs\Libs\Html\Markup\TextNode($overflow)) . "</p>";
    var_dump($overflow);
}

$container = new MarkupContainer;
$container->push(ModHub\safeHtml($content));
$container->push(ModHub\ht("p", "Hello"));

$listing1 = new Objects\Listing;

$listing1->addObject(
    id(new Objects\Object)
        ->setHeadline('News of the day')
);
$listing1->addObject(
    id(new Objects\Object)
        ->setHeadline('Breaking news')
);
$listing1->addObject(
    id(new Objects\Object)
        ->setHeadline('Paper of tomorrow')
        ->setHeadHref('http://www.cnn.com/')
);
$container->push($listing1->render());


$listing2 = new Objects\Listing;
$listing2->setTitle('Basic Listing with header and attributes');

$listing2->addObject(
    id(new Objects\Object)
        ->setHeadline('A little story of the future')
        ->addAttribute('Christian Müller')
        ->addAttribute('Yesterday')
        ->addAttribute('Don\'t believe me? ¬.¬')
);
$listing2->addObject(
    id(new Objects\Object)
        ->setHeadline('Why the future is the future')
        ->addAttribute('Christoph Müller')
        ->addAttribute('Two days before')
);
$listing2->addObject(
    id(new Objects\Object)
        ->setHeadline('Future, I am your father')
        ->addAttribute('Hans Müller')
        ->addAttribute('Two days before')
);
$container->push($listing2->render());


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
