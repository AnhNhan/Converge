<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Display;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
trait TChangeObject
{
    use TForumDisplayObject;

    protected function buildBasicPanel()
    {
        $panel = div("post-change-object");

        $title = new MarkupContainer;
        $title->push($this->buildProfileImage());
        $title->push(cv\ht("div", $this->date)->addClass("pull-right"));
        $title->push($this->getHeaderText());
        $panel->append($title);

        return $panel;
    }

    protected function buildProfileImage()
    {
        return cv\ht("img")
            ->addOption("src", $this->profile_image_uri)
            ->addClass("user-profile-image pull-left")
        ;
    }
}
