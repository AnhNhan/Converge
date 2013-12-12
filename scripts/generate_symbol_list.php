#!/usr/bin/php
<?php
require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\ModHub;
use YamwLibs\Functions\FileFunc;
use YamwLibs\Infrastructure\Printers\ArrayPrinter;
use YamwLibs\Infrastructure\Symbols\SymbolGenerator;
use YamwLibs\Infrastructure\Symbols\SymbolTreeGenerator;

// First get all interesting .php files
$rawFiles = array();
try {
    $retVal = -1;
    chdir(ModHub\get_root_super());
    exec('git ls-files --full-name -c src/', $rawFiles, $retVal);
    if ($retVal !== 0) {
        throw new Exception("Git failed!");
    }
    $files = FileFunc::sanitizeStringsFromPrefix(
        preg_grep("/\\.php$/", $rawFiles),
        'src/'
    );
} catch (Exception $exc) {
    ModHub\println("Could not retrieved index files from Git.");
    ModHub\println(
        "Falling back to file-based approach, which could index " .
        "unindexed files!"
    );
    $rawFiles = FileFunc::recursiveScanForDirectories(ModHub\path(), '\.php');
    $files = FileFunc::sanitizeStringsFromPrefix($rawFiles, ModHub\path());
}

$fileCount = count($files);

ModHub\println("Analyzing $fileCount files...");

// Now begins the cool part :D
$symbolGenerator = new SymbolGenerator();
$skipped = array();

foreach ($files as $file) {
    if (preg_match("/Test\\.php$/i", $file)) {
        echo "S";
        $skipped[] = $file;
        continue;
    }
    $symbolGenerator->parseFiles(array(ModHub\path($file)));
    echo ".";
}

$nodes = $symbolGenerator->getNodes();

ModHub\println();
if ($skipped) {
    ModHub\println("Skipped " . count($skipped) . " files:");
    echo "  - ";
    echo implode("\n  - ", $skipped);
    ModHub\println();
    ModHub\println();
}
ModHub\println("Successfully analyzed $fileCount files!");
ModHub\println("Found " . count($nodes) . " symbols.");

ModHub\println("Generating symbol tree...");

$symbolTreeGenerator = new SymbolTreeGenerator($nodes);
$symbolTreeGenerator->generate();

$symbolTree = $symbolTreeGenerator->getGeneratedTree();

ModHub\println("Successfully generated symbol tree!");
ModHub\println("Writing to disk...");

$arrayPrinter = new ArrayPrinter();

ob_start();
echo <<<EOT
<?php
// -----------------------------------------------------------------------------
/**
 *  This file was generated by the SymbolGenerator(tm)
 *  Would be cool if you wouldn't edit it, as that would sure break things
 *
 *  To re-generate this file, run `php -f scripts/generate_symbol_list.php`
 *
 *  Thank you
 *  @love Anh Nhan <anhnhan@outlook.com>
 *
 *  @generated
 */
// -----------------------------------------------------------------------------
EOT;

$symbolMapString = ob_get_clean();
$symbolMapString .= $arrayPrinter->printForFile($symbolTree);

file_put_contents(
    ModHub\path("__symbol_map__.php"),
    $symbolMapString
);

ModHub\println("Successfully wrote to disk!");
