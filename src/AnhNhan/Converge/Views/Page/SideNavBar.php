<?php
namespace AnhNhan\Converge\Views\Page;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\AbstractView;
use YamwLibs\Libs\View\ViewInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class SideNavBar extends AbstractView
{
    public function render()
    {
        $container = Converge\ht('div')->addClass('cv-side-navbar');

        $ul = Converge\ht('ul')->addClass('cv-side-navbar-items');
        $items = array(
            array(
                'text' => 'tags',
                'icon' => 'pricetags',
                'href' => 'tag/',
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
                'text' => Converge\safeHtml('{{queries}} &times; SQL'),
                'icon' => 'ios7-pie',
                'href' => '#',
            ),
            array(
                'text' => '{{memory}}MB',
                'icon' => 'disc',
                'href' => '#',
            ),
        );
        foreach ($items as $item) {
            $ul->append(
                Converge\ht('li')
                    ->addClass('cv-side-navbar-item')
                    ->append(
                        Converge\ht('a')
                            ->addOption('href', $item['href'])
                            ->addOption('data-backbone-nav', idx($item, 'backbone') ? '' : null)
                            ->append(div('cv-side-navbar-icon', Converge\icon_ion('', $item['icon'])))
                            ->append(div('cv-side-navbar-text', $item['text']))
                    )
            );
        }
        $container->append($ul);

        return $container;
    }
}
