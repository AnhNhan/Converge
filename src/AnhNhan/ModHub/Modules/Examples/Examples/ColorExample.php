<?php
namespace AnhNhan\ModHub\Modules\Examples\Examples;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ColorExample extends AbstractExample
{
    public function getName()
    {
        return "colors";
    }

    private $colors = array(
        "font" => true,
        "link" => true,
        "link-active" => true,
        "link-visited" => true,
        "header" => true,
        "white" => false,
        "black" => true,
        "grey" => true,
        "light-grey" => true,
        "very-light-grey" => false,
        "dark-grey" => true,
        "very-dark-grey" => true,
        "bright-red" => true,
        "dark-blue" => true,
        "cool-blue" => true,
        "olive-green" => true,
        "bright-olive-green" => true,
        "unsc" => true,
        "covenant" => true,
        "flood" => true,
        "yellow" => true,
        "pink" => true,
        "tinted-gred" => true,
        "bone-meal" => true,
        "purple" => true,
    );

    private $specialColors1 = array(
        "info" => true,
        "success" => true,
        "danger" => true,
        "warning" => true,
    );

    private $specialColors2 = array(
        "info-real" => true,
        "success-real" => true,
        "danger-real" => true,
        "warning-real" => true,
    );

    private $flatColors = array(
        "turquoise" => true,
        "emerland" => true,
        "peterriver" => true,
        "amethyst" => true,
        "wet-asphalt" => true,
        "greensea" => true,
        "nephritis" => true,
        "belize-hole" => true,
        "wisteria" => true,
        "midnight-blue" => true,
        "sunflower" => true,
        "carrot" => true,
        "alizarin" => true,
        "clouds" => true,
        "concrete" => true,
        "orange" => true,
        "pumpkin" => true,
        "pomegranate" => true,
        "silver" => true,
        "asbestos" => true,
    );

    private $headers = array(
        "Colors",
        "Special purpose",
        "", // Continuation
        "Flat UI colors",
    );

    public function getExample()
    {
        $container = new MarkupContainer;

        foreach (array($this->colors, $this->specialColors1, $this->specialColors2, $this->flatColors) as $key => $colors) {
            $container->push(h2(idx($this->headers, $key)));
            $colorContainer = new MarkupContainer;
            $container->push(div("clearfix", $colorContainer));
            $this->generateColors($colors, $colorContainer);
        }

        return $container;
    }

    private function generateColors(array $colors, MarkupContainer $container)
    {
        foreach ($colors as $color => $brightFont) {
            $container->push(ModHub\hsprintf(
                <<<EOT
<div class="%s pull-left" style="width: 20%%; height: 10em; padding-top: 7.6em; text-align: right;">
    <span style="color: %s; display: block; padding: 0.5em; background-color: rgba(55, 55, 55, 0.25);">
        %s
    </span>
</div>
EOT
                ,
                "color-bg-" . $color, // class
                $brightFont ? "#FFFFFF" : "#111111",// font color
                $color // text
            ));
        }
    }
}
