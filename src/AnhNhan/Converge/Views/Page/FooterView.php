<?php
namespace AnhNhan\Converge\Views\Page;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Symbols\SymbolLoader;
use AnhNhan\Converge\Views\AbstractView;
use YamwLibs\Libs\View\ViewInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class FooterView extends AbstractView implements ViewInterface
{
    private $columns = array();

    public function column($name, array $entries)
    {
        $this->columns[$name] = $entries;

        return $this;
    }

    public function render()
    {
        $container = Converge\ht('div')->addClass("footer");

        foreach ($this->columns as $name => $list) {
            $ul = Converge\ht('ul')->addClass('footer-section');
            $ul->append(Converge\ht('h3', $name));

            foreach ($list as $entry) {
                $li = Converge\ht('li')->addClass('footer-section-entry');

                $linkOptions = array_select_keys($entry, array_diff(array_keys($entry), array('label')));

                $li->setContent(
                    Converge\ht(
                        'a',
                        Converge\ht('span', $entry['label']),
                        $linkOptions
                    )
                );

                $ul->append($li);
            }

            $container->append($ul);
        }

        return $container;
    }

    public static function getDefaultFooter()
    {
        $footer = new static();

        // Sad, but empty :/

        return $footer;
    }
}
