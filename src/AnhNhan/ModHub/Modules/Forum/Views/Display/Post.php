<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Display;

use AnhNhan\ModHub as mh;
use AnhNhan\ModHub\Views\AbstractView;
use AnhNhan\ModHub\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Post extends AbstractView
{
    private $username;
    private $profile_image_uri;

    private $date;
    private $bodyText;

    private $buttons;

    public function __construct()
    {
        parent::__construct();
        $this->buttons = new MarkupContainer;
    }

    public function setUserDetails($username, $profileimage)
    {
        $this->username = $username;
        $this->profile_image_uri = $profileimage;
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

    public function render()
    {
        $postPanel = new Panel;

        $title = new MarkupContainer;
        $title->push(
            mh\ht("img")
                ->addOption("src", $this->profile_image_uri)
                ->addClass("user-profile-image")
        );
        $title->push(mh\ht("div", $this->date)->addClass("pull-right"));
        $title->push(mh\hsprintf("<div><strong>%s</strong> added a comment</div>", $this->username));
        $postPanel->setHeader($title);

        $postPanel->append($this->buttons);
        $postPanel->append($this->bodyText);

        return $postPanel->render(); // Rendering so the user can apply classes etc.
    }
}
