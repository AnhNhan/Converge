<?php
namespace AnhNhan\ModHub\Modules\Examples\Examples;

use AnhNhan\ModHub\Modules\StaticResources\ResMgr;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class AbstractExample
{
    final public function __construct()
    {
        // Empty constructor
    }

    abstract public function getName();

    abstract public function getExample();

    /*
     * @var ResMgr
     */
    private $resMgr;

    public function setResMgr(ResMgr $resMgr)
    {
        $this->resMgr = $resMgr;
        return $this;
    }

    public function getResMgr()
    {
        if (!$this->resMgr) {
            throw new \RunTimeException(
                sprintf(
                    "Tried to access non-existing ResMgr service from class '%s'!",
                    get_class($this)
                )
            );
        }
        return $this->resMgr;
    }
}
