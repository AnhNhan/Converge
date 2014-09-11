<?php
namespace AnhNhan\Converge\Modules\Examples\Examples;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class PanelExample extends AbstractExample
{
    public function getName()
    {
        return 'panel';
    }

    public function getExample()
    {
        $container = new MarkupContainer;

        // De-register libphutil autoloader, can't be used with Faker
        spl_autoload_unregister('__phutil_autoload');

        $faker = \Faker\Factory::create();

        $panel = new Panel;
        $panel->setHeader(h3('Some panel header'));
        $panel->append(Converge\ht('p', 'Random body. Somebody write something here!'));
        $container->push($panel->render());
        $container->push($panel->setColor(Panel::COLOR_DANGER)->render());
        $container->push($panel->setColor(Panel::COLOR_INFO)->render());
        $container->push($panel->setColor(Panel::COLOR_SUCCESS)->render());

        $panel = new Panel;
        $panel->setHeader(h2('Some terrible discussion'));
        $panel->midriff()->push(Converge\safeHtml('<strong>Anh Nhan</strong> did something <em>silly</em>.'));
        $panel->setMidriffRight(date("D, d M 'y", time()));
        $panel->append(Converge\ht('p', $faker->text(800)));

        $list = Converge\ht('ul');
        for ($ii = 0; $ii < 5; $ii++) {
            $list->append(Converge\ht('li', span('', $faker->text(20))));
        }
        $panel->append($list);

        $panel->append(Converge\ht('p', $faker->text(800)));

        $container->push($panel);

        $panel = (new Panel)
            ->setColor(Panel::COLOR_DARK)
        ;
        $panel->midriff()->push(Converge\icon_ion('Deleted Post', 'close', false));
        $container->push($panel);

        $panel = (new Panel)
            ->setColor(Panel::COLOR_DARK)
            ->setHeader('Winter is coming')
            ->append(Converge\ht('p', $faker->text(200)))
        ;
        $panel->midriff()->push(Converge\safeHtml('Said <em>who</em>?'));
        $container->push($panel);

        return $container;
    }
}
