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
        $container->push(ModHub\safeHtml(file_get_contents(ModHub\path("/../resources/templates/front/frontpage.html"))));

        $payload = new HtmlPayload();
        $payload->setPayloadContents($container);
        return $payload;
    }
}
