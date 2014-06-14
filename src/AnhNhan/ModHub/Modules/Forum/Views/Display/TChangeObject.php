<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Display;

use AnhNhan\ModHub as mh;
use AnhNhan\ModHub\Views\Panel\Panel;
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
        $title->push(mh\ht("div", $this->date)->addClass("pull-right"));
        $title->push($this->getHeaderText());
        $panel->appendContent($title);

        return $panel;
    }

    protected function buildProfileImage()
    {
        return mh\ht("img")
            ->addOption("src", $this->profile_image_uri)
            ->addClass("user-profile-image pull-left")
        ;
    }
}
