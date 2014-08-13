<?php

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Symbols\SymbolLoader;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class GlobalTest extends \PHPUnit_Framework_TestCase
{
    public function testEverythingCanBeParsed()
    {
        SymbolLoader::setStaticRootDir(Converge\path());
        $symbolLoader = SymbolLoader::getInstance();
        $symbolLoader->register();
        $symbolLoader->loadAllSymbols();

        // We succeeded!
        self::assertTrue(true);
    }
}
