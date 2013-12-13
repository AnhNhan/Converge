<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\AbstractView;
use AnhNhan\ModHub\Views\Objects;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class SidebarView extends AbstractView
{
    public function render()
    {
        $menu_nav = ModHub\ht("div")
            ->addClass("menu-nav");

        $menu_nav->appendContent(ModHub\ht("h2", "Upcoming events"));

		$listing = new Objects\Listing;
		$menu_nav->appendContent($listing);

		$listing->addObject(
		    id(new Objects\Object)
		        ->setHeadline("Swag Throwaway Thursday")
		        ->addAttribute("27th June 2014")
		        ->addAttribute("324 participants")
		);

		$listing->addObject(
		    id(new Objects\Object)
		        ->setHeadline("Community AmA")
		        ->addAttribute("29th June 2014")
		        ->addAttribute("985 participants")
		);

		$listing->addObject(
		    id(new Objects\Object)
		        ->setHeadline("SotP 3.2")
		        ->addAttribute("Release")
		        ->addAttribute("4th July 2014")
		        ->addAttribute("985 participants")
		);

        return $menu_nav;
    }
}
