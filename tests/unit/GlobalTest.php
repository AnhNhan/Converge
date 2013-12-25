<?php

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class GlobalTest extends \PHPUnit_Framework_TestCase
{
    public function testEverythingCanBeParsed()
    {
        SymbolLoader::setStaticRootDir(ModHub\path());
        $symbolLoader = SymbolLoader::getInstance();
        $symbolLoader->register();
        $symbolLoader->loadAllSymbols();

        // We succeeded!
        self::assertTrue(true);
    }
}
