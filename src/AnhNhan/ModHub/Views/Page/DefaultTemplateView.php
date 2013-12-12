<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\AbstractView;
use AnhNhan\ModHub\Views\Page\HtmlDocumentView;
use YamwLibs\Libs\Html\HtmlFactory as HF;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DefaultTemplateView extends AbstractView
{
    private $title;
    private $content;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function getContent()
    {
        $head_wrapper = HF::divTag()->addClass("header-wrap");
        $header = HF::divTag()
            ->addClass("header")
            ->setContent(
            ModHub\ht("p", "Header comes here")
        );
        $head_wrapper->appendContent($header);

        $sidebar = HF::divTag()
            ->addClass("sidebar")
            ->setContent(ModHub\ht("p", "Menu comes here"));

        $content = HF::divTag()
            ->addClass("content")
            ->setContent($this->content);

        $container = HF::divTag('', 'content-container', 'content-container');

        $container->appendContent($head_wrapper);
        $container->appendContent($sidebar);
        $container->appendContent($content);
        $container->appendContent(FooterView::getDefaultFooter()->render());

        $wrapper = HF::divTag($container, 'wrapper', 'wrapper');

        return $wrapper;
    }

    public function render()
    {
        $htmlDocument = new HtmlDocumentView(
            $this->title,
            $this->getContent(),
            null
        );
        return $htmlDocument->render();
    }
}
