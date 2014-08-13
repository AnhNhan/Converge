<?php
namespace AnhNhan\Converge\Web\Application;

use AnhNhan\Converge\Views\Page\DefaultTemplateView;
use AnhNhan\Converge\Modules\StaticResources\ResMgr;

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

    private $user_details;

    public function setUserDetails($user_details)
    {
        $this->user_details = $user_details;
        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function renderHttpBody()
    {
        $defaultTemplate = new DefaultTemplateView($this->title, $this->getPayloadContents());
        $defaultTemplate->setResMgr($this->getResMgr());
        $defaultTemplate->setUserDetails($this->user_details);
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
