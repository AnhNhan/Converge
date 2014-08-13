<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Display;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\AbstractView;
use AnhNhan\Converge\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class ForumDisplayObject extends AbstractView
{
    use TForumDisplayObject;

    protected function buildBasicPanel()
    {
        $panel = new Panel;

        $title = new MarkupContainer;
        $title->push($this->buildProfileImage());
        if ($this->getDetail())
        {
            $title->push(cv\ht("div", $this->getDetail())->addClass("pull-right"));
        }
        $title->push($this->getHeaderText());
        $panel->setHeader($title);

        return $panel;
    }

    protected function getDetail()
    {
        return null;
    }

    protected function buildProfileImage()
    {
        return cv\ht("img")
            ->addOption("src", $this->profile_image_uri)
            ->addClass("user-profile-image")
        ;
    }

    protected function getHeaderText()
    {
        return "";
    }
}
