<?php
namespace AnhNhan\ModHub;

use YamwLibs\Libs\Html\Markup\SafeTextNode;
use YamwLibs\Libs\Html\Markup\HtmlTag;

/**
 * Use with care. Could allow for XSS attacks!
 *
 * @param mixed $string
 *
 * @return SafeTextNode
 */
function safeHtml($string)
{
    return new SafeTextNode($string);
}

/**
 * Creates a HTML tag
 *
 * @param string $name
 * @param mixed $content
 * @param array $options
 *
 * @return HtmlTag
 */
function ht($name, $content = null, array $options = array())
{
    if (isset($options["backbone"]) || isset($options["bckbn"])) {
        $options[] = "data-backbone-nav";
        unset($options["backbone"]);
        unset($options["bckbn"]);
    }
    return new HtmlTag($name, $content, $options);
}

function icon_text($text, $icon, $isRight = true, $white = false)
{
    $white_class = $white ? " icon-white" : null;
    return safeHtml(
        sprintf(
            '%s<i class="icon-%s%s"></i>%s',
            !$isRight ? $text . " " : "",
            $icon,
            $white_class,
            $isRight ? $text . " " : ""
        )
    );
}
