<?php
namespace AnhNhan\ModHub\Web\Application;

use YamwLibs\Libs\Http\Request;

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

    abstract public function handle();
}
