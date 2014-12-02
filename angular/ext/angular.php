<?php

$contents = [];
$dirname = __DIR__ . '/angular-1.3.0/';
foreach (array_reverse(scandir($dirname)) as $entry) {
    if (in_array($entry, array('.', '..'))) {
        continue;
    }

    if (!preg_match('/\.js$/i', $entry) || preg_match('/\.min\.js$/i', $entry))
    {
        continue;
    }

    $filename = $dirname . $entry;
    if (is_dir($filename))
    {
        continue;
    }

    $contents[] = file_get_contents($filename);
}

header("Content-Type: application/javascript");

echo implode("\n// ---------------------------\n", $contents);
