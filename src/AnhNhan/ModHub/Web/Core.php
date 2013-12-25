<?php
namespace AnhNhan\ModHub\Web;

use AnhNhan\ModHub;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;

use Symfony\Component\HttpFoundation\Request;

/**
 * Bootstrap of the application
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class Core
{
    private $router;

    public function dispatchRequest(Request $request)
    {
        if (!$this->router) {
            $this->router = new AppRouting($this->buildAppList());
        }

        return $this->router->routeToController($request);
    }

    private function buildAppList()
    {
        static $classes;
        if (!$classes) {
            $classes = SymbolLoader::getInstance()
                ->getConcreteClassesThatDeriveFromThisOne('AnhNhan\ModHub\Web\Application\BaseApplication');
        }
        return $classes;
    }
}
