<?php
namespace AnhNhan\Converge\Web\Application;

use AnhNhan\Converge\Web\Application\HtmlPayload;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class BaseApplicationController
{
    use \AnhNhan\Converge\Infrastructure\MagicGetter;

    /**
     * @var BaseApplication
     */
    private $app;

    /**
     * @var RequestStack
     */
    private $requestStack;

    final public function __construct(BaseApplication $app)
    {
        $this->app = $app;
    }

    final public function app()
    {
        return $this->app;
    }

    final protected function externalApp($name)
    {
        return $this->app->getService('app.list')->app($name);
    }

    final protected function isAppEnapled($name)
    {
        return $app = $this->externalApp($name) && $app->isApplicationEnabled();
    }

    protected function getRepository($entityName)
    {
        $app = $this->app();
        $entityManager = $app->getEntityManager();
        return $entityManager->getRepository($entityName);
    }

    /**
     * @return Request
     */
    final public function request()
    {
        return $this->requestStack->getCurrentRequest();
    }

    final public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        return $this;
    }

    public function requiredUserRoles($request)
    {
        return [];
    }

    private function checkUserRoles($user = null, array $required_roles)
    {
        if (!$user)
        {
            throw new \Exception('You need to be logged in to view this page!');
        }

        $roles = $user->roles;
        $not_included = [];
        // Using pull here and all later, so we can construct error messages
        $result = map(function ($role) use ($roles, &$not_included) {
            $result = isset($roles[$role]);
            if (!$result)
            {
                $not_included[] = $role;
            }
            return $result;
        }, $required_roles);
        if (!all($result))
        {
            throw new \Exception('You require the following roles: ' . implode(', ', $not_included));
        }

        return true;
    }

    final protected function isGranted($attributes, $object = null)
    {
        $security_context = $this->app->getService('security.context');
        if (!$security_context->getToken())
        {
            return false;
        }
        return $security_context->isGranted($attributes, $object);
    }

    final public function doProcessing()
    {
        $request = $this->request;
        $required_roles = $this->requiredUserRoles($request);
        $required_roles and $this->checkUserRoles($this->user, $required_roles);

        return $this->process();
    }

    /**
     * Override this method if you want to handle multiple types of data
     *
     * @return AnhNhan\Converge\Web\HttpPayload|Symfony\Component\HttpFoundation\Response
     */
    public function process()
    {
        return $this->handle();
    }

    /**
     * @return AnhNhan\Converge\Web\HttpPayload|Symfony\Component\HttpFoundation\Response
     */
    abstract public function handle();

    final protected function payload_html($contents = null, $flags = [])
    {
        $payload = new HtmlPayload($contents, $flags);
        $payload->setResMgr($this->resMgr);

        $user = $this->user;
        if ($user && is_object($user))
        {
            $payload->setUserDetails([
                'uid'        => $user->uid,
                'username'   => $user->name,
                'canon_name' => $user->canonical_name,
                'image_path' => $user->getGravatarImagePath(40),
            ]);
        }

        return $payload;
    }

    // |-----------------------------------------------------------------------|
    // |----  Various stuff, service location etc.  ---------------------------|
    // |-----------------------------------------------------------------------|

    private static $_user;

    protected function user()
    {
        if (self::$_user)
        {
            return self::$_user;
        }
        $session = $this->app->getService('session');
        if ($session && $session->has('_security_token'))
        {
            $token = $session->get('_security_token');
            $user = $token->getUser();
            if ($user && is_object($user))
            {
                self::$_user = $user;
                return self::$_user;
            }
        }
    }

    protected function resMgr()
    {
        return $this->app->getService("resource_manager");
    }

    protected function stopwatch()
    {
        return $this->app->getService("stopwatch");
    }

    protected function dispatchEvent($event_name, Event $event = null)
    {
        return $this->app->getService('event_dispatcher')->dispatch($event_name, $event);
    }

    protected function internalSubRequest($uri, array $params = [], $method = 'GET', $catch = true)
    {
        $request = Request::create($uri, $method, $params);
        $request->server->set('REQUEST_URI', $uri);

        $kernel = $this->app->getService('http_kernel');
        return $kernel->handle($request, HttpKernelInterface::SUB_REQUEST, $catch);
    }

    protected function getDraftObject($draft_object_key)
    {
        if ($user = $this->user)
        {
            $result = $this->internalSubRequest(
                urisprintf('draft/%s/%s', $user->uid, $draft_object_key)
            );
            if ($result->getStatusCode() != 200)
            {
                return null;
            }
            return idx(json_decode($result->getContent(), true), 'payloads');
        }
    }

    protected function deleteDraftObject($draft_object_key)
    {
        if ($user = $this->user)
        {
            $result = $this->internalSubRequest(
                urisprintf('draft/%s/%s', $user->uid, $draft_object_key), [], 'DELETE'
            );
        }
    }
}
