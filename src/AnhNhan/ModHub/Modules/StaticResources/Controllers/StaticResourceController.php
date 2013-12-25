<?php
namespace AnhNhan\ModHub\Modules\StaticResources\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Web\Application\RawHttpPayload;

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

        $payload = new RawHttpPayload();

        if ($type == "css") {
            $payload->setHttpHeader("Content-Type", "text/css");
        } else if ($type == "js") {
            $payload->setHttpHeader("Content-Type", "application/javascript");
        }

        $contents = file_get_contents(ModHub\get_root_super() . "/cache/" . $name);
        $payload->setPayloadContents($contents);

        return $payload;
    }
}
