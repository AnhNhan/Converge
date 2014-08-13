<?php
namespace AnhNhan\Converge\Modules\Examples\Examples;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use AnhNhan\Converge\Views\Objects;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class GenericStyles extends AbstractExample
{
    public function getName()
    {
        return "generic-styles";
    }

    private $tag_colors = [
        'default', // this one is fake
        'dark',
        'blue',
        'green',
        'success',
        'info',
        'warning',
        'danger',
    ];

    private $button_sizes = [
        'default', // this one is fake
        'small',
        'large',
    ];

    private $button_colors = [
        'primary',
        'default',
        'success',
        'info',
        'warning',
        'danger',
    ];

    public function getExample()
    {
        $container = new MarkupContainer;

        $container->push($tags = div()->appendContent(h2('Tags')));
        $this->buildTags($tags, $this->tag_colors);

        $container->push($buttons = div()->appendContent(h2('Buttons')));
        $this->buildButtons($buttons, $this->button_sizes, $this->button_colors);
        $this->buildButtons($buttons, ['disabled'], $this->button_colors, true);

        $container->push($listing = div()->appendContent(h2('Object Listing')));
        $this->buildObjectListing($listing);

        return $container;
    }

    private function buildObjectListing($container)
    {
        $container->appendContent($listing1 = new Objects\Listing);
        $listing1
            ->setTitle('Title here.')
            ->addObject(id(new Objects\Object)
                ->setHeadline('Big news')
                ->addAttribute('some attribute')
                ->addAttribute('other things')
            )
            ->addObject(id(new Objects\Object)
                ->setHeadline('Big news')
                ->addAttribute('some attribute')
                ->addAttribute('other things')
                ->addDetail('1st April 2015')
                ->addDetail('by Nyan Cat')
            )
            ->addObject(id(new Objects\Object)
                ->setHeadline('Important news')
                ->setByLine('very important subtitle')
                ->addAttribute('foo')
                ->addAttribute('bar')
                ->addDetail('1st April 2015')
                ->addDetail('by Nyan Cat')
            )
        ;
    }

    private function buildTags($container, array $tag_colors)
    {
        array_walk($tag_colors, function ($c) use ($container) {
            $container->appendContent(new TagView($c, $c));
        });
    }

    private function buildButtons($container, array $button_sizes, array $button_colors, $disabled = null)
    {
        array_walk($button_sizes, function ($s) use ($container, $button_colors, $disabled) {
            $container->appendContent($buttons = div()->appendContent(h3(ucwords($s))));
            $this->buildButtonsForSize($buttons, $s, $button_colors, $disabled);
        });
    }

    private function buildButtonsForSize($container, $size, array $button_colors, $disabled = null)
    {
        array_walk($button_colors, function ($c) use ($container, $size, $disabled) {
            $class = "btn btn-$c";

            $disableClassExtensions = [
                'default'  => true,
                'disabled' => true,
            ];
            if (!isset($disableClassExtensions[$size])) {
                $class .= ' btn-' . $size;
            }
            $container->appendContent(
                a(ucwords("$size $c"), 'example/generic-styles/#')
                    ->addClass($class)
                    ->addOption('disabled', $disabled)
                );
        });
    }
}
