<?php
include __DIR__ . '/../vendor/autoload.php';

use YamwLibs\Functions\FileFunc;

$files = FileFunc::recursiveScanForDirectories(__DIR__ . '/modules', '.js');

header("Content-Type: application/javascript");

echo implode("\n\n// ------------------------------------------------\n\n", array_map('file_get_contents', $files));
