<?php
namespace AnhNhan\ModHub\Web;

use AnhNhan\ModHub;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

/**
 * The HttpKernel handles a Request and converts it to a Response
 *
 * Some methods like those for event handling are derived from Symfony HttpKernel
 * implementation
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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EventDispatcherInterface
     */
    private $container;

    public function __construct(EventDispatcherInterface $dispatcher, AppRouting $appRouter)
    {
        $this->dispatcher = $dispatcher;
        $this->appRouter = $appRouter;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        return $this;
    }

    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        try {
            return $this->handleRequest($request, $type);
        } catch (\Exception $exc) {
            if (is_null($catch) || $catch === false) {
                throw $exc;
            }

            return $this->handleException($exc, $request, $type);
        }
    }

    private function handleRequest(Request $request, $type)
    {
        Core::startOverFlowCapturing();
        if ($this->container) {
            $container = $this->container;
        } else {
            $container = Core::loadSfDIContainer();
        }

        $container->set("request", $request);

        $resMgr = new ResMgr(ModHub\path("__resource_map__.php"));
        $container->set("resource_manager", $resMgr);

        $event = new GetResponseEvent($this, $request, $type);
        $this->dispatcher->dispatch(KernelEvents::REQUEST, $event);

        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request, $type);
        }

        $app = $this->appRouter->routeToApplication($request);
        $app->setContainer($container);
        $controller = $app->routeToController($request);

        if ($controller) {
            $event = new FilterControllerEvent($this, array($controller, 'handle'), $request, $type);
            $this->dispatcher->dispatch(KernelEvents::CONTROLLER, $event);
            $controller = $event->getController();

            if (!is_object($controller) && is_callable($controller) && is_array($controller) && is_object(idx($controller, 0)) && idx($controller, 1) == 'handle') {
                // It's our lovely controllers!
                $controller = $controller[0];
            }

            if (!($controller instanceof Application\BaseApplicationController)) {
                throw new \RunTimeException("This HttpKernel does not know how to" .
                    "handle controllers of type '" . get_class($controller) . "'.");
            }

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

        $response = Core::prepareResponse($request, $payload, $this->getCapturedOverflow());
        return $this->filterResponse($response, $request, $type);
    }

    private function filterResponse(Response $response, Request $request, $type)
    {
        $event = new FilterResponseEvent($this, $request, $type, $response);
        $this->dispatcher->dispatch(KernelEvents::RESPONSE, $event);
        $this->finishRequest($request, $type);
        return $event->getResponse();
    }

    private function finishRequest(Request $request, $type)
    {
        $this->dispatcher->dispatch(KernelEvents::FINISH_REQUEST, new FinishRequestEvent($this, $request, $type));
    }

    private function getCapturedOverflow()
    {
        $overflow = Core::endOverFlowCapturing();

        // TODO: Build a more elegant solution
        $overflowContents = null;
        if ($overflow) {
            ob_start();
            echo "<div style=\"text-align: left; margin: 1em;\">";
            echo "<h3>We had overflow!</h3>";
            echo "<pre>" . (new \YamwLibs\Libs\Html\Markup\TextNode($overflow)) . "</pre>";
            echo "</div>";
            $overflowContents = ob_get_clean();
        }

        return $overflowContents;
    }

    private function handleException(\Exception $e, $request, $type)
    {
        // Totally copied from Symfony implemenation
        $event = new GetResponseForExceptionEvent($this, $request, $type, $e);
        $this->dispatcher->dispatch(KernelEvents::EXCEPTION, $event);

        // a listener might have replaced the exception
        $e = $event->getException();

        if (!$event->hasResponse()) {
            $this->finishRequest($request, $type);

            throw $e;
        }

        $response = $event->getResponse();

        // the developer asked for a specific status code
        if ($response->headers->has('X-Status-Code')) {
            $response->setStatusCode($response->headers->get('X-Status-Code'));

            $response->headers->remove('X-Status-Code');
        } elseif (!$response->isClientError() && !$response->isServerError() && !$response->isRedirect()) {
            // ensure that we actually have an error response
            if ($e instanceof HttpExceptionInterface) {
                // keep the HTTP status code and headers
                $response->setStatusCode($e->getStatusCode());
                $response->headers->add($e->getHeaders());
            } else {
                $response->setStatusCode(500);
            }
        }

        try {
            return $this->filterResponse($response, $request, $type);
        } catch (\Exception $e) {
            return $response;
        }
    }
}