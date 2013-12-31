<?php
namespace AnhNhan\ModHub\Modules\StaticResources\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Web\Application\RawHttpPayload;
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

        BA::assertIsEnum($type, array("css", "js"));

        $resMap = include ModHub\path("__resource_map__.php");

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
        }

        $maxAge = 60 * 60 * 24 * 360 * 2;
        $response = Response::create()
            ->setCache(array(
                "etag" => $resource["hash"],
                "public" => true,
                "max_age" => $maxAge,
                "s_maxage" => $maxAge,
            ))
        ;
        $response->headers->set("Content-Type", $contentType);

        if ($notModified = $response->isNotModified($request)) {
            return $response;
        }

        $contents = file_get_contents(ModHub\get_root_super() . "/cache/" . $name);
        $response->setContent($contents);
        return $response;
    }
}
