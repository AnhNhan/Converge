<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\AbstractView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class SidebarView extends AbstractView
{
    public function render()
    {
        $menu_nav = ModHub\ht("div")
            ->addClass("menu-nav")
            ->setContent(ModHub\ht("p", "Content goes here"));

        return $menu_nav;
    }
}
