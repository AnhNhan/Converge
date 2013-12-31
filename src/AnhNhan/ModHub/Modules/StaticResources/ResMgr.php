<?php
namespace AnhNhan\ModHub\Modules\StaticResources;

use YamwLibs\Libs\Assertions\BasicAssertions as BA;
use YamwLibs\Libs\Assertions\FileAssertions as FA;

/**
 * Manages required resources from resource map
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class ResMgr
{
    private $resourceMap = array();

    private $resources = array(
        'css' => array(),
        'js'  => array(),
        'pck' => array(),
    );

    public function __construct($path = '__resource_map__.php')
    {
        FA::assertFileExists($path);
        $this->resourceMap = include $path;
    }

    private function pushResource($stackName, $resource)
    {
        BA::assertIsEnum($stackName, array('css', 'js'));

        if (!isset($this->resources[$stackName][$resource])) {
            $this->resources[$stackName][$resource] = true;
        }
        return $this;
    }

    public function requireCSS($name)
    {
        return $this->pushResource('css', $name);
    }

    public function requireJS($name)
    {
        return $this->pushResource('js', $name);
    }

    public function fetchIncludedResourcesForType($type)
    {
        $resources = array();
        foreach ($this->resources[$type] as $res => $_) {
            $resEntry = $this->attemptToReadFromResMap($type, $res);
            $resName = array($res, $resEntry['hash']);
            $resources[] = $resName;
        }
        return $resources;
    }

    public function fetchRequiredCSSResources()
    {
        return $this->fetchIncludedResourcesForType("css");
    }

    public function fetchRequiredJSResources()
    {
        return $this->fetchIncludedResourcesForType("js");
    }

    public function resourceExists($type, $name)
    {
        // Not checking pack files
        return isset($this->resourceMap[$type]) && isset($this->resourceMap[$type][$name]);
    }

    public function getHashForResource($type, $name)
    {
        return idx($this->attemptToReadFromResMap($type, $name), "hash");
    }

    private function attemptToReadFromResMap($type, $name)
    {
        $entry = idx($this->resourceMap[$type], $name);

        if (!$entry) {
            $entry = idx($this->resourceMap["pck"], $name, array());
        }

        return $entry;
    }
}
