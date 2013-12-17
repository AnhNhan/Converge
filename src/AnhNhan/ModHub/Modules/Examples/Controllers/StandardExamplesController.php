<?php
namespace AnhNhan\ModHub\Modules\Examples\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Web\Application\BaseApplicationController;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use YamwLibs\Infrastructure\Symbols\SymbolLoader;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class StandardExamplesController extends BaseApplicationController
{
    public function handle()
    {
        $request = $this->request();
        $exampleName = $request->getValue("name");

        $classes = SymbolLoader::getInstance()
            ->getClassesThatDeriveFromThisOne('AnhNhan\ModHub\Modules\Examples\Examples\AbstractExample');
        $instances = array();
        foreach ($classes as $class) {
            $instances[] = new $class;
        }
        $examples = mpull($instances, null, "getName");

        $example = idx($examples, $exampleName);
        if ($example) {
            $example = $example->getExample();
        } else {
            $example = ModHub\ht("h1", "Example " . $exampleName . " not found!");
        }

        $payload = new HtmlPayload($example);
        return $payload;
    }
}
