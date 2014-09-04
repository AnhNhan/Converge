<?php
namespace AnhNhan\Converge\Web\Application;

use AnhNhan\Converge\Web\Application\HtmlPayload;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class BaseApplicationController
{
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
        $result = pull($required_roles, function ($role) use ($roles, &$not_included) {
            $result = isset($roles[$role]);
            if (!$result)
            {
                $not_included[] = $role;
            }
            return $result;
        });
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

    final protected function payload_html()
    {
        $payload = new HtmlPayload;
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


    // Magic properties :)
    // $this->app instead of $this->app()
    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        }

        throw new \RunTimeException(sprintf(
            "Ayo, you tried to access '%s::%s' that does not exist in here!\n" .
            "Better check your code!",
            get_class($this),
            $name
        ));
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

    protected function internalSubRequest($uri, array $params = [], $method = 'GET', $catch = true)
    {
        $request = Request::create($uri, $method, $params);
        $request->server->set('REQUEST_URI', $uri);

        $kernel = $this->app->getService('http_kernel');
        return $kernel->handle($request, HttpKernelInterface::SUB_REQUEST, $catch);
    }
}
