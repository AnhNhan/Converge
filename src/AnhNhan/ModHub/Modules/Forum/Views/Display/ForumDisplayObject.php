<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Display;

use AnhNhan\ModHub as mh;
use AnhNhan\ModHub\Views\AbstractView;
use AnhNhan\ModHub\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class ForumDisplayObject extends AbstractView
{
    protected $username;
    protected $profile_image_uri;

    protected $header;
    protected $date;

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

    protected function buildBasicPanel()
    {
        $panel = new Panel;

        $title = new MarkupContainer;
        $title->push($this->buildProfileImage());
        $title->push(mh\ht("div", $this->date)->addClass("pull-right"));
        $title->push($this->getHeaderText());
        $panel->setHeader($title);

        return $panel;
    }

    protected function buildProfileImage()
    {
        return mh\ht("img")
            ->addOption("src", $this->profile_image_uri)
            ->addClass("user-profile-image")
        ;
    }

    protected function getHeaderText()
    {
        return "";
    }
}
