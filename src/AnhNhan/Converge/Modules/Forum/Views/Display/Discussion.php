<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Display;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
use AnhNhan\Converge\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Discussion extends ForumDisplayObject
{
    private $bodyText;

    private $buttons;
    private $tags;

    public function __construct()
    {
        parent::__construct();
        $this->buttons = new MarkupContainer;
        $this->tags    = new MarkupContainer;
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

    public function addTagView(TagView $tag)
    {
        $this->tags->push($tag);
        return $this;
    }

    public function addTag($text, $color = null)
    {
        $this->tags->push(new TagView($text, $color));
        return $this;
    }

    public function addTagObject(Tag $tag)
    {
        $this->tags->push(link_tag($tag, TagLinkExtra_None));
        return $this;
    }

    public function render()
    {
        $discussionPanel = new Panel;

        $headerRiff = new MarkupContainer;
        $headerRiff->push($this->buildProfileImage());

        $headerContainer = div();
        $headerContainer->append(h2($this->header));

        $small = cv\hsprintf(
            '<div><h3>%s <span class="minor-stuff">created this discussion on %s</span></h3></div>',
            $this->username,
            $this->date
        );

        $headerContainer->append($small);
        $headerRiff->push($headerContainer);
        $discussionPanel->setHeader($headerRiff);

        $discussionPanel->append($this->bodyText);

        $midriff = $discussionPanel->midriff();
        if ($this->tags->count()) {
            $midriff->push($this->tags);
        } else {
            $midriff->push(cv\ht("small", "No tags for this discussion")->addClass("muted"));
        }
        $discussionPanel->setMidriffRight($this->buttons);

        return $discussionPanel;
    }
}
