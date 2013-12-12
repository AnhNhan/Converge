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
    return new HtmlTag($name, $content, $options);
}

function icon_text($text, $icon, $white = false)
{
    $white_class = $white ? " icon-white" : null;
    return safeHtml(
        sprintf(
            "<i class=\"icon-%s%s\"></i> %s",
            $icon,
            $white_class,
            $text
        )
    );
}
