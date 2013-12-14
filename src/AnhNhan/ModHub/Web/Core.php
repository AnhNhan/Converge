<?php
namespace AnhNhan\ModHub\Web;

use AnhNhan\ModHub;
use YamwLibs\Infrastructure\Config\Config;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;
use YamwLibs\Infrastructure\Symbols\SymbolLoader;
use YamwLibs\Libs\Http\Request;
use YamwLibs\Libs\Routing\Router;

/**
 * Bootstrap of the application
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class Core
{
    private $renderWithTemplate;

    public function init($page)
    {
        ResMgr::init(ModHub\path("__resource_map__.php"));

        $config = new Config;
        $request = new Request;
        $request->setConfig($config);
        $request->populate(array("page" => $page));

        $page = '/'.$request->getValue('page');
        $opage = $page;

        $this->action_string = explode('/', $page);

        $get_params_from_uri = $this->findOutUriGetParams($request, $page);
        $host_name = $this->findOutHostName($request, $get_params_from_uri);

        $request->populate(array(
            "uri-action-string" => $page,
            "uri-get-params" => $get_params_from_uri,
            "uri-hostname" => $host_name,
        ));

        return $request;
    }

    public function routeToApplication($uri, array $applications = null)
    {
        if (!$applications) {
            $applications = $this->buildAppList();
        }
        $apps = array();
        foreach ($applications as $class_name) {
            $apps[] = new $class_name;
        }

        $app_routes = mpull($apps, "getRoutes");

        $router = new Router;
        foreach ($app_routes as $routes) {
            foreach ($routes as $route) {
                $router->registerRoute($route);
            }
        }

        return $router->route($uri);
    }

    private function buildAppList()
    {
        static $classes;
        if (!$classes) {
            $classes = SymbolLoader::getInstance()
                ->getClassesThatDeriveFromThisOne('AnhNhan\ModHub\Web\Application\BaseApplication');
        }
        return $classes;
    }

    private function findOutUriGetParams($request, $page)
    {
        $request->populateFromServer(
            array(
                'QUERY_STRING' => '/',
                'REQUEST_URI' => '/',
                'PHP_SELF' => 'index.php'
            )
        );
        // Remove the query url (after rewrite) from the URL
        $temp_page = str_replace($request->getValue('server-query_string'), '', $request->getValue('server-request_uri'));
        // Remove the script filename itself
        $temp_page = str_replace(basename($request->getValue('server-php_self')), '', $temp_page);

        // Remove the base URL
        // http://localhost.dev/yourapp/home/index => http://localhost.dev/yourapp
        $temp_page = str_replace($page, '', $temp_page);

        return $temp_page;
    }

    private function findOutHostName($request, $remaining_uri)
    {
        $request->populateFromServer(
            array(
                'HTTP_HOST' => 'localhost',
            )
        );

        // If we pass additional url parameters, they shouldn't appear in the base url
        // /home/index?bla=2hi => /home/index
        $temp_page = preg_replace("/(.*?)\?.*?$/i", '$1', $remaining_uri);
        $temp_page = preg_replace("/(.*?)&.*?$/i", '$1', $temp_page);

        $temp_page = str_replace("\n", '', $temp_page);

        if (!$temp_page) { // Empty query string
            $temp_page = '/';
        } else {
            if (!preg_match('/\/$/si', $temp_page)) { // No trailing slash
                $temp_page .= '/';
            }
            if (!preg_match('/^\//si', $temp_page)) { // No slash at beginning
                $temp_page = '/'.$temp_page;
            }
        }

        $host = $request->getValue('server-http_host');
        $page = "http://{$host}{$temp_page}";

        if (!preg_match('/\/$/si', $page)) {
            $page .= '/';
        }

        return $page;
    }
}
