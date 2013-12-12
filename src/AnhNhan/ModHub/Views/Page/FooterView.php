<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub;
use YamwLibs\Libs\View\ViewInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class FooterView implements ViewInterface
{
    private $columns = [];

    public function column($name, array $entries)
    {
        $this->columns[$name] = $entries;

        return $this;
    }

    public function render()
    {
        $container = ModHub\ht('div')->addClass("footer");

        foreach ($this->columns as $name => $list) {
            $ul = ModHub\ht('ul')->addClass('footer-section');
            $ul->appendContent(ModHub\ht('h3', $name));

            foreach ($list as $entry) {
                $li = ModHub\ht('li')->addClass('footer-section-entry');

                $li->setContent(
                    ModHub\ht(
                        'a',
                        ModHub\ht('span', $entry['label']),
                        ['href' => $entry['href']]
                    )
                );

                $ul->appendContent($li);
            }

            $container->appendContent($ul);
        }

        return $container;
    }

    public static function getDefaultFooter()
    {
        $footer = new static();

        $column1 = [
            [
                "label" => "Mailaddressensuche",
                "href"  => "/allg/mailsearch/",
            ],
            [
                "label" => "Telefonliste",
                "href"  => "/allg/phonesearch/",
            ],
            [
                "label" => "Umfragen",
                "href"  => "/allg/umfragen/",
            ],
        ];
        $footer->column('Allgemeines', $column1);

        $column2 = [
            [
                "label" => "Netiquette",
                "href"  => "/allg/netiquette/",
            ],
            [
                "label" => "WLAN",
                "href"  => "/allg/wifi/",
            ],
            [
                "label" => "Ergebnisse der Umfragen",
                "href"  => "/allg/mailsearch/",
            ],
        ];
        $footer->column('Infos', $column2);

        return $footer;
    }
}
