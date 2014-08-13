<?php
namespace AnhNhan\ModHub\Web;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\StaticResources\ResMgr;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * The HttpKernel handles a Request and converts it to a Response
 *
 * Some methods like those for event handling are derived from Symfony HttpKernel
 * implementation
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class HttpKernel implements HttpKernelInterface
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

    /**
     * @var RequestStack
     */
    private $request_stack;

    public function __construct(EventDispatcherInterface $dispatcher, AppRouting $appRouter, RequestStack $request_stack = null)
    {
        $this->dispatcher = $dispatcher;
        $this->appRouter = $appRouter;
        $this->request_stack = $request_stack ?: new RequestStack;
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
            $this->setContainer($container = Core::loadSfDIContainer());
        }
        $this->request_stack->push($request);

        $container->set("request", $request);

        $resMgr = new ResMgr(ModHub\path("__resource_map__.php"));
        $container->set("resource_manager", $resMgr);

        $event = new GetResponseEvent($this, $request, $type);
        $this->dispatcher->dispatch(KernelEvents::REQUEST, $event);

        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request, $type);
        }

        $app = $this->appRouter->routeToApplication($request);
        if (!$app) {
            return $this->create404Response($request, $type);
        }
        $app->setContainer($container);
        $controller = $app->routeToController($request);

        if ($controller) {
            $event = new FilterControllerEvent($this, array($controller, 'process'), $request, $type);
            $this->dispatcher->dispatch(KernelEvents::CONTROLLER, $event);
            $controller = $event->getController();

            if (!is_object($controller) && is_callable($controller) && is_array($controller) && is_object(idx($controller, 0)) && idx($controller, 1) == 'process') {
                // It's our lovely controllers!
                $controller = $controller[0];
            }

            if (!($controller instanceof Application\BaseApplicationController)) {
                throw new \RunTimeException("This HttpKernel does not know how to" .
                    "handle controllers of type '" . get_class($controller) . "'.");
            }

            $payload = $controller->setRequestStack($this->request_stack)->process();
        } else {
            return $this->create404Response($request, $type);
        }

        if (!($payload instanceof Application\HttpPayload) && !($payload instanceof Response)) {
            throw new \RunTimeException(sprintf("Controller '%s' must return a HTTP payload or response!", get_class($controller)));
        }

        if ($payload instanceof Application\HtmlPayload || method_exists($payload, 'setResMgr')) {
            $payload->setResMgr($resMgr);
        }

        if ($payload instanceof Application\HtmlPayload) {
            $session = $container->get('session');
            if ($session && $session->has('_security_token'))
            {
                $token = $session->get('_security_token');
                $user = $token->getUser();
                if ($user && is_object($user))
                {
                    $payload->setUserDetails([
                        'username' => $user->name,
                        'canon_name' => $user->canonical_name,
                        'image_path' => $user->getGravatarImagePath(40),
                    ]);
                }
            }
        }

        if ($payload instanceof Application\HttpPayload) {
            $response = Core::prepareResponse($request, $payload, $this->getCapturedOverflow());
        } else {
            // Already a response, no need to convert
            $response = $payload;

            // Into the void!
            $this->getCapturedOverflow();
        }

        return $this->filterResponse($response, $request, $type);
    }

    private function create404Response($request, $type)
    {
        $payload = Core::get404Page($request->getPathInfo());
        $payload->setResMgr($this->container->get('resource_manager'));
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
        $this->request_stack->pop();
    }

    private function getCapturedOverflow()
    {
        $overflow = Core::endOverFlowCapturing();

        // TODO: Build a more elegant solution
        $overflowContents = null;
        if ($overflow) {
            ob_start();
            echo "<div style=\"text-align: left;margin-top: 60px; margin-left: 85px;\">";
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
