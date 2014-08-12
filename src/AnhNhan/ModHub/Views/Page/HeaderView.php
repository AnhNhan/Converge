<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub as mh;
use AnhNhan\ModHub\Modules\User\Storage\User;
use AnhNhan\ModHub\Modules\User\Views\UserPlateView;
use AnhNhan\ModHub\Views\AbstractView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class HeaderView extends AbstractView
{
    public function render()
    {
        $header_content = div("header-content");

        $header_content
            ->appendContent(
                div("nav-user")
                    ->appendContent(div("nav-user-img", mh\ht("img")->addOption("src", User::DEFAULT_PROFILE_IMAGE)))
                    ->appendContent(
                        div("nav-user-box")
                            ->appendContent(div("nav-user-box-name", "@FooBar"))
                            ->appendContent(
                                div("nav-user-box-detail")
                                    ->appendContent(mh\icon_ion("0", "android-mail"))
                            )
                    )
            )
        ;

        $header_content
            ->appendContent(
                div("nav-action")
                    ->appendContent(a(mh\icon_ion("", "search"), "disq/search")->addClass("btn btn-default"))
                    ->appendContent(a(mh\icon_ion("", "ios7-chatboxes-outline"), "disq/create")->addClass("btn btn-default"))
                    ->appendContent(a(mh\icon_ion("", "ios7-gear"), "o/settings")->addClass("btn btn-default"))
            )
        ;

        $header_content
            ->appendContent(
                div("nav-join")
                    ->appendContent(a("Join Us!", "join")->addClass("btn btn-info"))
                    ->appendContent(a("Login", "login")->addClass("btn btn-primary"))
            )
        ;

        $header_link = a(null, "/", true)
            ->appendContent(h2("hMod Hub"))
            ->appendContent(h4("A Great Journey is to be pursued. Greatness Awaits."))
        ;
        $header_content->appendContent($header_link);

        return $header_content;
    }
}
