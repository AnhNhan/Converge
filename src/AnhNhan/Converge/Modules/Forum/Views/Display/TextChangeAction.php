<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Display;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\AbstractView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class TextChangeAction extends AbstractView
{
    use TChangeObject;

    protected $prevText = "";
    protected $nextText = "";

    public function setPrevText($text)
    {
        $this->prevText = $text ?: "";
        return $this;
    }

    public function setNextText($text)
    {
        $this->nextText = $text ?: "";
        return $this;
    }

    final public function render()
    {
        $panel = $this->buildBasicPanel();
        $body = $this->renderBody();
        if ($body) {
            $panel->appendContent($body);
            $panel->addClass("panel-diff");
        }
        return $panel;
    }

    abstract protected function renderBody();

    protected function getHeaderText()
    {
        return cv\hsprintf(
            "<div><strong>%s</strong> changed %s</div>",
            $this->username,
            $this->getSubject()
        );
    }

    abstract protected function getSubject();
}
