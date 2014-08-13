<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Display;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
trait TForumDisplayObject
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
}
