<?php
namespace AnhNhan\ModHub\Web\Application;

use Symfony\Component\HttpFoundation\Request;

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
     * @var Request
     */
    private $request;

    final public function __construct(BaseApplication $app)
    {
        $this->app = $app;
    }

    final public function app()
    {
        return $this->app;
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
        return $this->request;
    }

    final public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Override this method if you want to handle multiple types of data
     *
     * @return AnhNhan\ModHub\Web\HttpPayload|Symfony\Component\HttpFoundation\Response
     */
    public function process()
    {
        return $this->handle();
    }

    /**
     * @return AnhNhan\ModHub\Web\HttpPayload|Symfony\Component\HttpFoundation\Response
     */
    abstract public function handle();


    // Magic properties :)
    // $this->app instead of $this->app()
    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name;
        }

        throw new \RunTimeException(sprintf(
            "Ayo, you tried to access '%s::%s' that does not exist in here!\n" .
            "Better check your code!",
            get_class($this),
            $name
        ));
    }
}
