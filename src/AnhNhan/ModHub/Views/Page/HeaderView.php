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
            ->appendContent(ModHub\ht("h1", ModHub\ht("a", "hMod Hub", array("href" => "/", "backbone" => true))))
            ->appendContent(ModHub\ht("h3", "A Great Journey is to be pursued. Greatness Awaits."))
        ;

        return $header_content;
    }
}
