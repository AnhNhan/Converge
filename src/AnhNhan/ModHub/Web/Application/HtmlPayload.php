<?php
namespace AnhNhan\ModHub\Web\Application;

use AnhNhan\ModHub\Views\Page\DefaultTemplateView;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;

/**
 * Renders the payload with the default template
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class HtmlPayload extends HttpPayload
{
    private $title = "Untitled Page";

    /*
     * @var ResMgr
     */
    private $resMgr;

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function renderHttpBody()
    {
        $defaultTemplate = new DefaultTemplateView($this->title, $this->getPayloadContents());
        $defaultTemplate->setResMgr($this->getResMgr());
        return $defaultTemplate;
    }

    protected function getDefaultContentType()
    {
        return "text/html";
    }

    public function setResMgr(ResMgr $resMgr)
    {
        $this->resMgr = $resMgr;
        return $this;
    }

    public function getResMgr()
    {
        return $this->resMgr;
    }
}
