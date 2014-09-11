<?php
namespace AnhNhan\Converge\Modules\Draft;

use AnhNhan\Converge\Web\Application\BaseApplication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DraftApplication extends BaseApplication
{
    public function getHumanReadableName()
    {
        return 'Draft';
    }

    public function getInternalName()
    {
        return 'draft';
    }

    public function getRoutes()
    {
        return $this->generateRoutesFromYaml(__DIR__ . '/resources/routes.yml');
    }

    public function routeToController(Request $request)
    {
        $routeName = $request->attributes->get('route-name');

        switch ($routeName) {
            case 'draft-user-object':
                return new Controllers\Draft($this);
                break;
        }

        return null;
    }

    protected function buildEntityManager($dbConfig)
    {
        return $this->buildDefaultEntityManager($dbConfig, array(__DIR__ . '/Storage'));
    }
}
