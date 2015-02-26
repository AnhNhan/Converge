<?php
namespace AnhNhan\Converge\Modules\Examples\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Web\Application\BaseApplicationController;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use AnhNhan\Converge\Modules\Symbols\SymbolLoader;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ExampleListing extends BaseApplicationController
{
    public function handle()
    {
        $instances = SymbolLoader::getInstance()
            ->getObjectsThatDeriveFrom('AnhNhan\Converge\Modules\Examples\Examples\AbstractExample');
        $examples = mpull($instances, 'getName');
        foreach ($examples as $example) {
            $title = preg_replace('/[-]/', ' ', $example);
            $title = ucwords($title);
            $list[] = array(
                'label' => $title,
                'href'  => "example/$example/"
            );
        }

        $container = new MarkupContainer;

        foreach ($list as $example) {
            $container->push(div('', a($example['label'], $example['href'])->addClass('btn btn-default')));
        }

        $payload = $this->payload_html();
        $payload->setPayloadContents($container);
        $payload->setTitle('Examples');
        return $payload;
    }
}
