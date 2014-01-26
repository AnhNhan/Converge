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
        $head_wrapper = div("header", new HeaderView, "header");
        $sideNavBar = new SideNavBar;

        $content = div("content width12")
            ->setId("content")
            ->setContent($this->content);

        $contentContainerContent = div("row-flex");
        $contentContainerContent->appendContent($content);

        $container = div('layout-container grid-system', null, 'layout-container');

        $container->appendContent($contentContainerContent);
        $container->appendContent(FooterView::getDefaultFooter());

        $wrapper = HF::divTag($container, 'wrapper', 'wrapper');

        return id(new MarkupContainer)
            ->push($head_wrapper)
            ->push($sideNavBar)
            ->push($wrapper)
        ;
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
        $resMgr = $this->getResMgr()
            ->prependCSS("core-pck")
            ->prependJS("libs-pck");
        $htmlDocument->setResMgr($resMgr);
        return $htmlDocument;
    }
}
