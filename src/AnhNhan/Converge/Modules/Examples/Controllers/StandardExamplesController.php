<?php
namespace AnhNhan\Converge\Modules\Examples\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Web\Application\BaseApplicationController;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use AnhNhan\Converge\Modules\Symbols\SymbolLoader;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class StandardExamplesController extends BaseApplicationController
{
    public function handle()
    {
        $request = $this->request();
        $exampleName = $request->request->get('name');

        $classes = SymbolLoader::getInstance()
            ->getConcreteClassesThatDeriveFromThisOne('AnhNhan\Converge\Modules\Examples\Examples\AbstractExample');
        $instances = array();
        foreach ($classes as $class) {
            $instances[] = new $class;
        }
        $examples = mpull($instances, null, 'getName');

        $example = idx($examples, $exampleName);
        if ($example) {
            $example->setResMgr($this->app->getService('resource_manager'));
            $example = $example->getExample();
        } else {
            $example = Converge\ht('h1', 'Example ' . $exampleName . ' not found!');
        }

        $payload = $this->payload_html($example);

        $title = ucwords(preg_replace('/[-]/', ' ', $exampleName)) . ' Example';
        $payload->setTitle($title);

        return $payload;
    }
}
