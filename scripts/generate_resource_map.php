#!/usr/bin/php
<?php
require_once __DIR__ . "/../src/__init__.php";

use AnhNhan\ModHub;
use YamwLibs\Infrastructure\Printers\ArrayPrinter;
use YamwLibs\Infrastructure\ResMgmt\Builders\CssBuilder;
use YamwLibs\Functions\FileFunc;

define("RESOURCE_DIR", "resources" . DIRECTORY_SEPARATOR);
define("CACHE_PATH", ModHub\get_root_super() . "cache" . DIRECTORY_SEPARATOR);
define("RESOURCE_MAP_PATH", "src/__resource_map__.php");
define("CSS_DIR", RESOURCE_DIR . "/css/");

if (!file_exists(CSS_DIR) || !is_dir(CSS_DIR)) {
    ModHub\println(CSS_DIR . " does not contain a folder with CSS files.");
    exit(1);
}

// First get all interesting resource files
$rawFiles = FileFunc::recursiveScanForDirectories(CSS_DIR, "\\.(less|css)");
$files = FileFunc::sanitizeStringsFromPrefix($rawFiles, CSS_DIR);

$rawPckFiles = FileFunc::recursiveScanForDirectories(RESOURCE_DIR, "\\.json");
$pckFiles = preg_grep(
    "/json$/",
    FileFunc::sanitizeStringsFromPrefix($rawPckFiles, RESOURCE_DIR)
);

$prependFiles = preg_grep("/^less/", $files);
foreach ($prependFiles as $prependFile) {
    unset($files[array_search($prependFile, $files)]);
}

ModHub\println("Found " . count($rawFiles) . " in total for processing.");
ModHub\println("Of these " . count($prependFiles) . " are prepend files.");
ModHub\println("Also found " . count($pckFiles) . " pack files.");
ModHub\println();

if (!file_exists(CACHE_PATH)) {
    ModHub\println("Cache dir does not exist, creating.");
    mkdir(CACHE_PATH);
}

$resMap = array(
    "css" => [],
    "js" => [],
    "pck" => [],
);

// Process CSS
$resCss =& $resMap["css"];

ModHub\println("Will now process LESS/CSS files.");

ModHub\println("Using the following prepend files:");
$prependContents = [];
foreach ($prependFiles as $prependFile) {
    ModHub\println("  - " . $prependFile);
    $prependContents[] = file_get_contents(CSS_DIR . $prependFile);
}
$prependContents = implode("\n\n", $prependContents);
ModHub\println();

ModHub\println("Files:");

foreach ($files as $cssFile) {
    $cssPath = CSS_DIR . $cssFile;
    $cssName = preg_replace("/\.(less|css)$/", "", $cssFile);
    $cssName = preg_replace(
        "/([\\|\/])+/",
        "-",
        $cssName
    );

    $cssEntry = array(
        "name" => $cssName,
        "path" => $cssFile,
        "hash" => hash_file("crc32", $cssPath),
    );

    // TODO: Replace file URIs to CDN URIs
    $cssContents = file_get_contents($cssPath);
    $cssContents = CssBuilder::buildString(
        $prependContents . $cssContents
    );

    if (file_put_contents(CACHE_PATH . $cssName, $cssContents)) {
        ModHub\println("  - " . str_pad($cssName, 40) . " [x]");
    } else {
        ModHub\println("  - " . str_pad($cssName, 40) . " [ ]");
    }

    $resCss[$cssName] = $cssEntry;
}

ModHub\println();

if ($pckFiles) {
    ModHub\println("Will now process pack files.");
    foreach ($pckFiles as $pck) {
        $pckFile = json_decode(file_get_contents(RESOURCE_DIR . $pck));

        if (!$pckFile) {
            ModHub\println("Skipping " . $pck);
            continue;
        }
        $pckFile = (array)$pckFile;
        $name = $pckFile["name"];

        $contents = [];
        $fileContents = [];
        foreach ($pckFile["contents"] as $contentName) {
            $contents[$contentName] = $resCss[$contentName]["hash"];
            $fileContents[] = file_get_contents(CACHE_PATH . $contentName);
        }
        file_put_contents(
            CACHE_PATH . $name,
            implode("\n\n", $fileContents)
        );

        $resMap["pck"][$name] = array(
            "name" => $name,
            "type" => "pck",
            "hash" => hash_hmac(
                "crc32",
                implode("", $contents),
                "random string"
            ),
            "contents" => $contents,
        );
        ModHub\println("  - " . $name);
    }
}

// Process JS (not supported yet)
ModHub\println("Skipping on JS resources, since they are not supported yet.");
ModHub\println();

ModHub\println("Finished processing static resources.");
ModHub\println("Writing " . RESOURCE_MAP_PATH . " to disk...");

ob_start();
echo <<<EOT
<?php
// -----------------------------------------------------------------------------
/**
 *  This file was generated for the static resource management system
 *  Would be cool if you wouldn"t edit it, as that would sure break things
 *
 *  To re-generate this file, run `php -f scripts/generate_resource_map.php`
 *
 *  Thank you
 *  @love Anh Nhan <anhnhan@outlook.com>
 *
 *  @generated
 */
// -----------------------------------------------------------------------------
EOT;

$arrayPrinter = new ArrayPrinter();
$resMapString = ob_get_clean();
$resMapString .= $arrayPrinter->printForFile($resMap);

file_put_contents(
    ModHub\get_root_super() . RESOURCE_MAP_PATH,
    $resMapString
);

ModHub\println("Successfully wrote to disk!");
