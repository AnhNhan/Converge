<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Display;

use AnhNhan\ModHub as mh;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class TextChangeAction extends ForumDisplayObject
{
    protected $prevText;
    protected $nextText;

    public function setPrevText($text)
    {
        $this->prevText = $text;
        return $this;
    }

    public function setNextText($text)
    {
        $this->nextText = $text;
        return $this;
    }

    final public function render()
    {
        $panel = $this->buildBasicPanel();
        $body = $this->renderBody();
        if ($body) {
            $panel->append($body);
        }
        return $panel;
    }

    abstract protected function renderBody();

    protected function getHeaderText()
    {
        return mh\hsprintf(
            "<div><strong>%s</strong> changed %s</div>",
            $this->username,
            $this->getSubject()
        );
    }

    abstract protected function getSubject();
}
