<?php
namespace AnhNhan\ModHub\Modules\Examples\Examples;

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
}
