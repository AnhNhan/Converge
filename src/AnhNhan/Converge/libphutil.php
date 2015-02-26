<?php

/// Replacement for libphutil's phutil_utf8_shorten (which became PhutilUTF8StringTruncator)
/// because I'm too lazy to fix all occurences.
/// Copied directly from libphutil.
function phutil_utf8_shorten($string, $length, $terminal = "\xE2\x80\xA6") {
    return id(new PhutilUTF8StringTruncator())
        ->setMaximumGlyphs($length)
        ->setTerminator($terminal)
        ->truncateString($string);
}
