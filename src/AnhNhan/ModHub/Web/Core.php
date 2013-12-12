<?php
namespace AnhNhan\ModHub\Web;

use AnhNhan\ModHub;
use YamwLibs\Infrastructure\Config\Config;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;
use YamwLibs\Libs\Http\Request;

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

        $request->populateFromGet(array("template" => true));
        $this->renderWithTemplate = $request->getValue("get-template");

        $request->populateFromServer(
            array(
                'QUERY_STRING' => '/',
                'REQUEST_URI' => '/',
                'HTTP_HOST' => 'localhost',
                'PHP_SELF' => 'index.php'
            )
        );
        // Remove the query url (after rewrite) from the URL
        $temp_page = str_replace($request->getValue('server-query_string'), '', $request->getValue('server-request_uri'));
        // Remove the script filename itself
        $temp_page = str_replace(basename($request->getValue('server-php_self')), '', $temp_page);

        // Remove the base URL
        // http://localhost.dev/yourapp/home/index => http://localhost.dev/yourapp
        $temp_page = str_replace($opage, '', $temp_page);
        $temp_page = str_replace($page, '', $temp_page);

        // If we pass additional url parameters, they shouldn't appear in the base url
        // /home/index?bla=2hi => /home/index
        $temp_page = preg_replace("/(.*?)\?.*?$/i", '$1', $temp_page);
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
        $this->page = "http://{$host}{$temp_page}";

        if (!preg_match('/\/$/si', $this->page)) {
            $this->page .= '/';
        }
    }
}
