<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Display;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Post extends ForumDisplayObject
{
    private $bodyText;

    private $buttons;

    public function __construct()
    {
        parent::__construct();
        $this->buttons = new MarkupContainer;
    }

    public function setBodyText($text)
    {
        $this->bodyText = $text;
        return $this;
    }

    public function addButton($button)
    {
        $this->buttons->push($button);
        return $this;
    }

    public function render()
    {
        $postPanel = $this->buildBasicPanel();

        $postPanel->append($this->buttons);
        $postPanel->append($this->bodyText);

        return $postPanel;
    }

    protected function getHeaderText()
    {
        return cv\hsprintf("<div><strong>%s</strong> added a comment</div>", $this->username);
    }
}
