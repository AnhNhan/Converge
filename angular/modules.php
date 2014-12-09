<?php
include __DIR__ . '/../vendor/autoload.php';

use YamwLibs\Functions\FileFunc;

$files = FileFunc::recursiveScanForDirectories(__DIR__ . '/modules');
$files = array_filter($files, function ($file)
    {
        return preg_match('/\.js$/i', $file) && !preg_match('/\.min\.js$/i', $file) && !preg_match('/-model\.js$/i', $file);
    });

header("Content-Type: application/javascript");

echo implode("\n\n// ------------------------------------------------\n\n", array_map('file_get_contents', $files));
