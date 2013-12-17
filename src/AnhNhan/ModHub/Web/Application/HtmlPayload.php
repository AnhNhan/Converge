<?php
namespace AnhNhan\ModHub\Web\Application;

use AnhNhan\ModHub\Views\Page\DefaultTemplateView;

/**
 * Renders the payload with the default template
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class HtmlPayload extends HttpPayload
{
    private $title = "Untitled Page";

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function renderHttpBody()
    {
        return new DefaultTemplateView($this->title, $this->getPayloadContents());
    }
}
