<?php
namespace AnhNhan\ModHub\Test;

use AnhNhan\ModHub;
use YamwLibs\Infrastructure\Symbols\SymbolLoader;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class GlobalTest extends TestCase
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