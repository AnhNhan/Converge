<?php
namespace AnhNhan\Converge\Views\Web\Response;

use AnhNhan\Converge as cv;

use AnhNhan\Converge\Modules\StaticResources\ResMgr;

use AnhNhan\Converge\Web\Application\HttpPayload;
use AnhNhan\Converge\Web\Application\HtmlPayload;

use YamwLibs\Libs\Html\Markup\MarkupContainer;
use YamwLibs\Libs\Html\Markup\TextNode;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ResponseHtml404 extends HttpPayload
{
    private $resMgr;
    private $text;

    public function renderHttpBody()
    {
        $this->setHttpCode(404);

        $payload = new HtmlPayload;
        $payload->setTitle('Resource not found');
        $payload->setResMgr($this->resMgr);

        $markup = new MarkupContainer;
        $markup->push(cv\ht('h1', 'Resource not found'));
        if ($this->text)
        {
            $markup->push(new TextNode($this->text));
        }
        $markup->push(cv\ht('p', ':('));

        $payload->setPayloadContents($markup);
        return $payload->renderHttpBody();
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

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }
}
