<?php
namespace AnhNhan\ModHub\Modules\Front\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use AnhNhan\ModHub\Web\Application\BaseApplicationController;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class StandardFrontController extends BaseApplicationController
{
    public function handle()
    {
        $container = new MarkupContainer();
        $container->push(ModHub\safeHtml(file_get_contents(__DIR__ . "/../resources/template/front-page.htm")));

        $payload = new HtmlPayload();
        $payload->setPayloadContents(ModHub\ht("div", $container, array("style" => "padding: 1em;")));
        return $payload;
    }
}
