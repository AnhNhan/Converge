<?php
namespace AnhNhan\Converge\Modules\Front\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use AnhNhan\Converge\Web\Application\BaseApplicationController;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class StandardFrontController extends BaseApplicationController
{
    public function handle()
    {
        if ($this->user)
        {
            return new RedirectResponse('dash');
        }

        $container = new MarkupContainer();
        $container->push(Converge\safeHtml(file_get_contents(Converge\path("/../resources/templates/front/frontpage.html"))));

        $payload = $this->payload_html();
        $payload->setPayloadContents($container);
        return $payload;
    }
}
