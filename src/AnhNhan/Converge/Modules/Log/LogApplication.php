<?php
namespace AnhNhan\Converge\Modules\Log;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class LogApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return 'Logs & Profiling';
    }

    public function getInternalName()
    {
        return 'log';
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . '/resources/routes.yml');
    }

    public function routeToController(Request $request)
    {
        switch ($request->attributes->get('route-name')) {
        }
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . '/Storage'));
    }
}
