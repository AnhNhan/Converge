<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub;
use YamwLibs\Infrastructure\Symbols\SymbolLoader;
use YamwLibs\Libs\View\ViewInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class FooterView implements ViewInterface
{
    private $columns = array();

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
                        array('href' => $entry['href'])
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

        $column1 = array(
            array(
                "label" => "Mailaddressensuche",
                "href"  => "/allg/mailsearch/",
            ),
            array(
                "label" => "Telefonliste",
                "href"  => "/allg/phonesearch/",
            ),
            array(
                "label" => "Umfragen",
                "href"  => "/allg/umfragen/",
            ),
        );
        $footer->column('Allgemeines', $column1);

        $column2 = array(
            array(
                "label" => "Netiquette",
                "href"  => "/allg/netiquette/",
            ),
            array(
                "label" => "WLAN",
                "href"  => "/allg/wifi/",
            ),
            array(
                "label" => "Ergebnisse der Umfragen",
                "href"  => "/allg/mailsearch/",
            ),
        );
        $footer->column('Infos', $column2);

        $column3 = array();
        $classes = SymbolLoader::getInstance()
            ->getClassesThatDeriveFromThisOne('AnhNhan\ModHub\Modules\Examples\Examples\AbstractExample');
        $instances = array();
        foreach ($classes as $class) {
            $instances[] = new $class;
        }
        $examples = mpull($instances, "getName");
        foreach ($examples as $example) {
            $title = preg_replace("/[-]/", ' ', $example);
            $title = ucwords($title);
            $column3[] = array(
                "label" => $title,
                "href"  => "/example/$example/"
            );
        }
        $footer->column("Examples", $column3);

        return $footer;
    }
}
