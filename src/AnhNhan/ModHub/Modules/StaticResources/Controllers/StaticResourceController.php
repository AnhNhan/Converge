<?php
namespace AnhNhan\ModHub\Modules\StaticResources\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Web\Application\BaseApplication;
use AnhNhan\ModHub\Web\Application\BaseApplicationController;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class StaticResourceController extends AbstractStaticResourceController
{
	public function handle()
	{
		$request = $this->request();
		$type = $request->getValue("type");
		$name = $request->getValue("name");
		$rsrc_hash = $request->getValue("rsrc-hash");

		if ($type == "css") {
			header("Content-Type: text/css");
		} else if ($type == "js") {
			header("Content-Type: application/javascript");
		}

		echo file_get_contents(ModHub\get_root_super() . "/cache/" . $name);
	}
}