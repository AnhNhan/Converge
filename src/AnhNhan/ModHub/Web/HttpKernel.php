<?php
namespace AnhNhan\ModHub\Web;

use AnhNhan\ModHub;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * The HttpKernel handles a Request and converts it to a Response
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class HttpKernel implements HttpKernelInterface, ContainerAwareInterface
{
    /**
     * @var AppRouting
     */
    private $appRouter;

    /**
     * @var ContainerInterface
     */
    private $dpContainer;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->dpContainer = $container;
    }

    public function __construct(AppRouting $appRouter)
    {
        $this->appRouter = $appRouter;
    }

    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        Core::startOverFlowCapturing();
        $container = Core::loadSfDIContainer();

        $container->set("request", $request);

        $resMgr = new ResMgr(ModHub\path("__resource_map__.php"));
        $container->set("resource_manager", $resMgr);

        $app = $this->appRouter->routeToApplication($request);
        $app->setContainer($container);
        $controller = $app->routeToController($request);

        if ($controller) {
            $payload = $controller->setRequest($request)->handle();
        } else {
            $payload = self::get404Page($page);
        }
        if (!($payload instanceof Application\HttpPayload)) {
            throw new \RunTimeException(sprintf("Controller '%s' must return a HTTP payload!", get_class($controller)));
        }

        if ($payload instanceof Application\HtmlPayload) {
            $resMgr
                ->requireCSS("core-pck")
                ->requireJS("libs-pck");
            $payload->setResMgr($resMgr);
        }

        $overflow = Core::endOverFlowCapturing();
        $overflowContents = null;
        if ($overflow) {
            ob_start();
            echo "<div style=\"text-align: left; margin: 1em;\">";
            echo "<h3>We had overflow!</h3>";
            echo "<pre>" . (new \YamwLibs\Libs\Html\Markup\TextNode($overflow)) . "</pre>";
            echo "</div>";
            $overflowContents = ob_get_clean();
        }

        return Core::prepareResponse($request, $payload, $overflowContents);
    }
}
