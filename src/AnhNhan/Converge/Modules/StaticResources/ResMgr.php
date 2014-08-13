<?php
namespace AnhNhan\Converge\Modules\StaticResources;

use YamwLibs\Libs\Assertions\BasicAssertions as BA;
use YamwLibs\Libs\Assertions\FileAssertions as FA;

/**
 * Manages and tracks included static resources from resource map for inclusion in the rendered page.
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

    private function prependResource($stackName, $resource)
    {
        BA::assertIsEnum($stackName, array('css', 'js'));

        if (!isset($this->resources[$stackName][$resource])) {
            $this->resources[$stackName] = array_merge(array($resource => true), $this->resources[$stackName]);
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

    public function prependCSS($name)
    {
        return $this->prependResource('css', $name);
    }

    public function prependJS($name)
    {
        return $this->prependResource('js', $name);
    }

    public function fetchIncludedResourcesForType($type)
    {
        $includedStack = array();
        $resources = array();
        $theorithicallyIncludedResources = array();

        // Track resources from directly included resources and from pack files
        foreach ($this->resources[$type] as $res => $_) {
            $entry = $this->attemptToReadFromResMap($type, $res);
            $includedStack[$res] = $entry;
            if (isset($entry["contents"])) { // Pack file, track its contents, not the resource itself
                $theorithicallyIncludedResources = array_merge(
                    $theorithicallyIncludedResources,
                    array_keys($entry["contents"])
                );
            } else {
                $theorithicallyIncludedResources[] = $res;
            }
        }
        // Rebuild from $key -> $value to $value -> $key for faster membership testing
        $theorithicallyIncludedResources = array_combine(
            array_keys($theorithicallyIncludedResources),
            array_values($theorithicallyIncludedResources) // Actually any value, just have it the same size
        );

        foreach ($includedStack as $resName => $resEntry) {
            if (isset($theorithicallyIncludedResources[$resName])) {
                continue;
            }
            $res = array($resName, $resEntry['hash']);
            $resources[] = $res;
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
