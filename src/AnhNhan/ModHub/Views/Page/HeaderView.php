<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub;
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
        $header_content = ModHub\ht("div")
            ->addClass("header-content")
            ->appendContent(
                ModHub\ht("a", "new here?", array(
                        "style"       => "margin-right: 50px; margin-top: 0; font-size: 3em !important;",
                        "href"        => "join",
                        "data-toggle" => "tooltip",
                        "title"       => "we'll make it feel like at home :)",
                    ))
                    ->addClass("btn btn-large btn-success pull-right header-button")
            )
            ->appendContent(
                ModHub\ht("a", "coming back?", array(
                        "style"       => "margin-right: 30px; margin-top: 0; font-size: 3em !important;",
                        "href"        => "login",
                        "data-toggle" => "tooltip",
                        "title"       => "welcome back!",
                    ))
                    ->addClass("btn btn-large btn-default pull-right header-button")
            )
            ->appendContent(ModHub\ht("h1", ModHub\ht("a", "hMod Hub", array("href" => "/", "backbone" => true))))
            ->appendContent(ModHub\ht("h3", "A Great Journey is to be pursued. Greatness Awaits."))
        ;

        return $header_content;
    }
}
