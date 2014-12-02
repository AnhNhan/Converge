<?php
require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\Converge as cv;

define('ApiServerUri', '//api.converge.dev/');
define('ServerUri', '//converge.dev/');

$res_mgr = (new AnhNhan\Converge\Modules\StaticResources\ResMgr(__DIR__ . '/../src/__resource_map__.php'))
    ->requireCSS('static-styles-pck')
    ->requireCSS('core-pck')
    ->requireJS('libs-pck')
;

$indexTemplate = file_get_contents('index-template.html');

$replace = function ($name, $contents) use (&$indexTemplate)
{
    $indexTemplate = str_ireplace("[[$name]]", $contents, $indexTemplate);
};

$create_resource_link = function ($type, $name, $hash, $base_uri = ApiServerUri)
{
    if ($type == 'css')
    {
        return cv\ht('link')
            ->addOption('rel', 'stylesheet')
            ->addOption('type', 'text/css')
            ->addOption('charset', 'utf-8')
            ->addOption('href', sprintf($base_uri . 'rsrc/css/%s.%s.css', $name, $hash))
        ;
    }
    else if ($type == 'js')
    {
        return cv\ht('script')
            ->addOption('src', sprintf($base_uri . 'rsrc/js/%s.%s.js', $name, $hash))
        ;
    }
    else
    {
        throw new Exception('Don\'t know how to use this, eh?');
    }
};

$replace('BASE_HREF', ServerUri);
$replace('DEFAULT_HEADER_CSS', implode("\n", array_map(flatten(curry_fa($create_resource_link, 'css')), $res_mgr->fetchRequiredCSSResources())));
$replace('DEFAULT_HEADER_SCRIPTS', implode("\n", array_map(flatten(curry_fa($create_resource_link, 'js')), $res_mgr->fetchRequiredJSResources())));

echo $indexTemplate;
