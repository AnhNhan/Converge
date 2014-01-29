<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Display;

use AnhNhan\ModHub as mh;
use AnhNhan\ModHub\Modules\Tag\Views\TagView;
use AnhNhan\ModHub\Views\AbstractView;
use AnhNhan\ModHub\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Discussion extends AbstractView
{
    private $username;
    private $profile_image_uri;

    private $header;
    private $date;
    private $bodyText;

    private $buttons;
    private $tags;

    public function __construct()
    {
        parent::__construct();
        $this->buttons = new MarkupContainer;
        $this->tags    = new MarkupContainer;
    }

    public function setUserDetails($username, $profileimage)
    {
        $this->username = $username;
        $this->profile_image_uri = $profileimage;
        return $this;
    }

    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    public function setDate($date)
    {
        $this->date = $date;
        return $this;
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

    public function render()
    {
        $discussionPanel = new Panel;

        $headerRiff = new MarkupContainer;
        $headerRiff->push(
            mh\ht("img")
                ->addOption("src", $this->profile_image_uri)
                ->addClass("user-profile-image")
        );

        $headerContainer = div();
        $headerContainer->appendContent(h2($this->header));

        $small = mh\ht("small", mh\hsprintf(
            "<strong>%s</strong> created this discussion on %s",
            $this->username,
            $this->date
        ));

        $headerContainer->appendContent($small);
        $headerRiff->push($headerContainer);
        $discussionPanel->setHeader($headerRiff);

        $discussionPanel->append($this->bodyText);

        $midriff = $discussionPanel->midriff();
        if ($this->tags->count()) {
            $midriff->push($this->tags);
        } else {
            $midriff->push(mh\ht("small", "No tags for this discussion")->addClass("muted"));
        }
        $discussionPanel->setMidriffRight($this->buttons);

        return $discussionPanel->render();
    }
}
