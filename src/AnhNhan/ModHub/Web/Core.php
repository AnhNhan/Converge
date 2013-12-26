<?php
namespace AnhNhan\ModHub\Web;

use AnhNhan\ModHub;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bootstrap of the application
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class Core
{
    public function handlePage($page = null, Request $request = null)
    {
        self::startOverFlowCapturing();
        if (!$request) {
            $request = Request::createFromGlobals();
        }
        $request->request->add(array("page" => $page));
        $request->query->add(array("page" => $page));

        $controller = $this->dispatchRequestToController($request);

        if ($controller) {
            $payload = $controller->setRequest($request)->handle();
        } else {
            $payload = self::get404Page($page);
        }
        if (!($payload instanceof Application\HttpPayload)) {
            throw new \RunTimeException(sprintf("Controller '%s' must return a HTTP payload!", get_class($controller)));
        }

        $overflow = self::endOverFlowCapturing();
        $overflowContents = null;
        if ($overflow) {
            ob_start();
            echo "<div style=\"text-align: left; margin: 1em;\">";
            echo "<h3>We had overflow!</h3>";
            echo "<pre>" . (new \YamwLibs\Libs\Html\Markup\TextNode($overflow)) . "</pre>";
            echo "</div>";
            $overflowContents = ob_get_clean();
        }

        return $this->prepareResponse($request, $payload, $overflowContents);
    }

    public function prepareResponse(
        Request $request,
        Application\HttpPayload $payload,
        $overflow
    ) {
        $contents = $overflow . $payload->getRenderedHttpBody();
        $headers = $payload->getHttpHeaders();
        unset($headers[0]);
        $response = Response::create(
            $contents,
            $payload->getHttpCode(),
            $headers
            )
            ->prepare($request)
        ;

        return $response;
    }

    public static function startOverFlowCapturing()
    {
        ob_start();
    }

    public static function endOverFlowCapturing()
    {
        return ob_get_clean();
    }

    public static function get404Page($page)
    {
        $payload = new AnhNhan\ModHub\Web\Application\HtmlPayload;
        $container = new \YamwLibs\Libs\Html\Markup\MarkupContainer;
        $container->push(ModHub\ht("h1", "Failed to find a controller for '$page'"));
        $payload->setPayloadContents($container);
        $payload->setTitle("Page not found");
        return $payload;
    }

    private $router;

    public function dispatchRequestToController(Request $request)
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
