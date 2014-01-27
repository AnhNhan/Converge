<?php
namespace AnhNhan\ModHub;

use YamwLibs\Libs\Html\Markup\SafeTextNode;
use YamwLibs\Libs\Html\Markup\TextNode;
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
 * Format a HTML code. This function behaves like sprintf(), except that all
 * the normal conversions (like %s) will be properly escaped.
 *
 * Implements libphutil's xprintf()
 */
function hsprintf($html/* , ... */) {
  $args = func_get_args();
  array_shift($args);
  foreach ($args as &$arg) {
    $arg = TextNode::escape($arg);
  }
  return new SafeTextNode(vsprintf($html, $args));
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
    if (idx($options, "backbone") || idx($options, "bckbn")) {
        $options[] = "data-backbone-nav";
        unset($options["backbone"]);
        unset($options["bckbn"]);
    }
    return new HtmlTag($name, $content, $options);
}

/**
 * Renders an icon using Twitter Bootstrap v2 (outdated)
 *
 * @param string $text        The text to use
 * @param string $icon        The name of the icon
 * @param bool   $textIsRight Whether the text should be on the right side or not.
 * @param bool   $white       Whether to use black or white icons
 *
 * @return SafeTextNode
 *
 * @deprecated This is still using sprite sheets, meaning icons are fixed 14px.
 *             We're in the process of phasing this out in favor of font-based icons
 */
function icon_bs2($text, $icon, $textIsRight = true, $white = false)
{
    $white_class = $white ? " icon-white" : null;
    return hsprintf(
        '%s <i class="icon-%s%s"></i> %s',
        !$textIsRight ? $text : "",
        $icon,
        $white_class,
        $textIsRight ? $text : ""
    );
}

/**
 * Using `icon_ion` is recommended - we may only selectively load this in future.
 *
 * Renders a font-based icon generated using icons generated from http://icomoon.io.
 * For the configuration, see https://github.com/AnhNhan/icomoon-mh.
 *
 * @param string $text
 * @param string $icon_name
 * @param bool   $iconIsRight
 *
 * @return SafeTextNode
 */
function icon_ic($text, $icon_name, $iconIsRight = true)
{
    return hsprintf(
        '%s <i class="ic-%s"></i> %s',
        $iconIsRight ? $text : "",
        $icon_name,
        !$iconIsRight ? $text : ""
    );
}

/**
 * Renders a font-based icon of the ion framework v1.4.1.
 *
 * @param string $text
 * @param string $icon_name
 * @param bool   $iconIsRight
 *
 * @return SafeTextNode
 */
function icon_ion($text, $icon_name, $iconIsRight = true)
{
    return hsprintf(
        '%s <i class="ion-%s"></i> %s',
        $iconIsRight ? $text : "",
        $icon_name,
        !$iconIsRight ? $text : ""
    );
}
