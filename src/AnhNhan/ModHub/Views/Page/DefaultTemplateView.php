<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\AbstractView;
use AnhNhan\ModHub\Views\Page\HtmlDocumentView;
use YamwLibs\Libs\Html\HtmlFactory as HF;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DefaultTemplateView extends AbstractView
{
    private $title;
    private $content;
    private $header;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function getContent()
    {
        $header_contents = new MarkupContainer;
        $header_contents->push(ModHub\ht("h1", "hMod Hub"));
        $header_contents->push(ModHub\ht("h3", "A Great Journey is to be pursued. Greatness Awaits."));

        $head_wrapper = HF::divTag()->addClass("header-wrap container row width12");
        $header = HF::divTag()
            ->addClass("header width12")
            ->setContent(
            $header_contents
        );
        $head_wrapper->appendContent($header);

        $content = HF::divTag()
            ->addClass("content width8")
            ->setContent($this->content);

        $sidebar = HF::divTag()
            ->addClass("sidebar width4")
            ->setContent(ModHub\ht("p", "Sidebar goes here"));

        $contentContainerContent = ModHub\ht("div")->addClass("row-flex");
        $contentContainerContent->appendContent($sidebar);
        $contentContainerContent->appendContent($content);

        $container = HF::divTag('', 'content-container', 'content-container container');

        $container->appendContent($head_wrapper);
        $container->appendContent($contentContainerContent);
        $container->appendContent(FooterView::getDefaultFooter()->render());

        $wrapper = HF::divTag($container, 'wrapper', 'wrapper');

        return $wrapper;
    }

    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    public function render()
    {
        $htmlDocument = new HtmlDocumentView(
            $this->title,
            $this->getContent(),
            $this->header
        );
        return $htmlDocument->render();
    }
}
