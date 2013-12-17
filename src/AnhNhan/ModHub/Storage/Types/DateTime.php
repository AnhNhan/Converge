<?php
namespace AnhNhan\ModHub\Storage\Types;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class DateTime
{
    private $timestamp;

    public function __construct($timestamp)
    {
        if (!static::checkValidity($timestamp)) {
            throw new \InvalidArgumentException("Trying to create a DateTime object with invalid timestamp!");
        }
        $this->timestamp = $timestamp;
    }

    public static function checkValidity($timestamp)
    {
        return preg_match("/^\d+$/", $timestamp) === 1;
    }
}
