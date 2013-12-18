<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;
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

        $column2 = array(
            array(
                "label" => "Front page",
                "href"  => "/",
            ),
            array(
                "label" => "Markup Test",
                "href"  => "/markup/test/",
            ),
        );
        $footer->column('All kinds of stuff', $column2);

        $column3 = array();
        $classes = SymbolLoader::getInstance()
            ->getConcreteClassesThatDeriveFromThisOne('AnhNhan\ModHub\Modules\Examples\Examples\AbstractExample');
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
