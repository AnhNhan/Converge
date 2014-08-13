<?php
namespace AnhNhan\Converge\Modules\StaticResources\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Web\Application\RawHttpPayload;
use YamwLibs\Libs\Assertions\BasicAssertions as BA;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class StaticResourceController extends AbstractStaticResourceController
{
    public function handle()
    {
        $request = $this->request();
        $type = $request->request->get("type");
        $name = $request->request->get("name");
        $rsrc_hash = $request->request->get("rsrc-hash");
        $doCache = true;

        BA::assertIsEnum($type, array("css", "js"));

        if (strncmp("tmpl-", $name, strlen("tmpl-")) === 0) {
            $type = "tmpls";
        } else {
            if (!$request->request->has("rsrc-hash")) {
                // $name is of format 'foo.eab25d.css'
                if (1 === preg_match("/^(?P<name>.*?)(\\.(?P<hash>.*)\\.{$type})$/", $name, $matches)) {
                    $name = $matches["name"];
                    $rsrc_hash = $matches["hash"];
                } else {
                    // Fresh serving of 'foo.css'
                    $doCache = false;
                    $parts = explode(".", $name);
                    array_pop($parts);
                    $name = implode(".", $parts);
                }
            }
        }

        $resMap = include Converge\path("__resource_map__.php");
        if (!$resource = idx($resMap[$type], $name)) {
            // Could be a pack file
            $resource = idx($resMap["pck"], $name);
        }

        if (!$resource) {
            throw new \Exception("Resource '{$name}' does not exist!");
        }

        if ($type == "css") {
            $contentType = "text/css";
        } else if ($type == "js") {
            $contentType = "application/javascript";
        } else if ($type == "tmpls") {
            $contentType = "text/template";
        }

        $maxAge = 60 * 60 * 24 * 360 * 2;
        $cacheSettings = array(
            "etag" => $resource["hash"],
            "public" => true,
        );
        if ($doCache && !in_array($type, array("tmpls"))) {
            $cacheSettings += array(
                "max_age" => $maxAge,
                "s_maxage" => $maxAge,
            );
        }
        $response = Response::create()
            ->setCache($cacheSettings)
        ;
        $response->headers->set("Content-Type", $contentType);

        if ($doCache && $notModified = $response->isNotModified($request)) {
            return $response;
        }

        // Js resource and not pck file
        $fileExt = ($type == "js" && !isset($resource["type"])) ? ".js" : null;
        $contents = file_get_contents(Converge\get_root_super() . "/cache/" . $name . $fileExt);
        $response->setContent($contents);
        return $response;
    }
}
