<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\AbstractView;
use YamwLibs\Libs\View\ViewInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class SideNavBar extends AbstractView
{
    public function render()
    {
        $container = ModHub\ht('div')->addClass('mh-side-navbar');

        $ul = ModHub\ht('ul')->addClass('mh-side-navbar-items');
        $items = array(
            array(
                'text' => 'dash',
                'icon' => 'navicon-round',
                'href' => '/disq/',
                'backbone' => true,
            ),
            array(
                'text' => 'create',
                'icon' => 'compose',
                'href' => 'disq/create/',
            ),
            array(
                'text' => 'users',
                'icon' => 'person-stalker',
                'href' => 'users/',
            ),
            array(
                'text' => 'roles',
                'icon' => 'key',
                'href' => 'roles/',
            ),
            array(
                'text' => 'markup',
                'icon' => 'printer',
                'href' => 'markup/test/',
            ),
            array(
                'text' => 'examples',
                'icon' => 'ios7-filing',
                'href' => 'example/',
            ),
            array(
                'text' => '{{time}}',
                'icon' => 'ios7-timer',
                'href' => '#',
            ),
            array(
                'text' => ModHub\safeHtml('{{queries}} &times; SQL'),
                'icon' => 'ios7-pie',
                'href' => '#',
            ),
        );
        foreach ($items as $item) {
            $ul->appendContent(
                ModHub\ht('li')
                    ->addClass('mh-side-navbar-item')
                    ->appendContent(
                        ModHub\ht('a')
                            ->addOption('href', $item['href'])
                            ->addOption('data-backbone-nav', idx($item, 'backbone') ? '' : null)
                            ->appendContent(div('mh-side-navbar-icon', ModHub\icon_ion('', $item['icon'])))
                            ->appendContent(div('mh-side-navbar-text', $item['text']))
                    )
            );
        }
        $container->appendContent($ul);

        return $container;
    }
}
