<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub;
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
            ->appendContent(ModHub\ht("h1", "hMod Hub"))
            ->appendContent(ModHub\ht("h3", "A Great Journey is to be pursued. Greatness Awaits."));

        return $header_content;
    }
}
