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
        $head_wrapper = HF::divTag()->addClass("header-wrap row-flex width12");
        $header = HF::divTag()
            ->addClass("header width12")
            ->setContent(
            new HeaderView
        );
        $head_wrapper->appendContent($header);

        $content = HF::divTag()
            ->addClass("content width12")
            ->setContent($this->content);

        $contentContainerContent = ModHub\ht("div")->addClass("row-flex");
        $contentContainerContent->appendContent($content);

        $container = HF::divTag('', 'layout-container', 'layout-container grid-system');

        $container->appendContent($head_wrapper);
        $container->appendContent($contentContainerContent);
        $container->appendContent(FooterView::getDefaultFooter());

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
        $htmlDocument->setResMgr($this->getResMgr());
        return $htmlDocument;
    }
}
