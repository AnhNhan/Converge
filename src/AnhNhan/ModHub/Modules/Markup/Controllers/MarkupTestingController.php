<?php
namespace AnhNhan\ModHub\Modules\Markup\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\Form\FormView;
use AnhNhan\ModHub\Views\Form\Controls\TextAreaControl;
use AnhNhan\ModHub\Views\Form\Controls\SubmitControl;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use YamwLibs\Infrastructure\Templater\MarkupManager;
use YamwLibs\Infrastructure\Templater\Markup;
use YamwLibs\Infrastructure\Templater\Templater;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class MarkupTestingController extends AbstractMarkupController
{
    public function handle()
    {
        $template = file_get_contents(__DIR__ . "/../resources/template/testing.html");

        Templater::loadCache($template);
        Templater::setMarkupMgr($this->getMarkupMgr());
        Templater::generateTemplate();

        $generatedTemplate = Templater::retrieveTemplate();

        $payload = new HtmlPayload(ModHub\safeHtml($generatedTemplate));
        $payload->setTitle("Markup Testing");
        return $payload;
    }

    private function buildForm()
    {
        $form = new FormView;
        $form->setDualColumnMode(false);

        $form->append(id(new TextAreaControl())
            ->setValue(<<<EOT
Welcome to Markdown
===================

Introduction
------------

Hello, this is some *Markdown*. Make some **bold** text. Or write some `code`.

Links:
 - [Google][1]
 - [Microsoft][2]

Add in some other paragraph. Two line breaks, and you are set!
This way, single line breaks can act as a continuation of the previous sentence.
That's cool, hm?

* * *

Who put that line in the way!?

Let me put some code here
-------------------------

```
// TODO: Put this somewhere reasonable
ResMgr::init(ModHub\path("__resource_map__.php"));
ResMgr::getInstance()
    ->requireCSS("core-pck")
    ->requireJS("libs-pck");
```

Steps until world domination
----------------------------

 1. Declare your own country.
 2. Declare war against everybody.
 3. ???
 4. Profit!

> This is a quote!

Have fun!

Notice: Link references added here (only visible in source)

 [1]: http://google.com/
 [2]: http://microsoft.com/
EOT
            )
            ->setLabel("Text"));

        $form->append(id(new SubmitControl())
            ->addCancelButton('/markup/test/')
            ->addSubmitButton('Hasta la vista!'));

        return $form;
    }

    private function getMarkupMgr()
    {
        $markupMgr = new MarkupManager;
        $markupMgr->registerMarkup(new Markup\SimpleTemplateMarkup("form", "FORM", $this->buildForm()));
        return $markupMgr;
    }
}
