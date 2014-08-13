<?php
namespace AnhNhan\Converge\Modules\Front\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use AnhNhan\Converge\Web\Application\BaseApplicationController;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class StandardFrontController extends BaseApplicationController
{
    public function handle()
    {
        $container = new MarkupContainer();
        $container->push(Converge\safeHtml(file_get_contents(Converge\path("/../resources/templates/front/frontpage.html"))));

        $payload = new HtmlPayload();
        $payload->setPayloadContents($container);
        return $payload;
    }
}
