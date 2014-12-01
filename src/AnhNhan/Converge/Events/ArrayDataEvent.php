<?php
namespace AnhNhan\Converge\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class ArrayDataEvent extends Event
{
    private $data = [];

    public function __get($name)
    {
        if ($name == 'data')
        {
            return $this->data;
        }

        throw new \RunTimeException();
    }

    public function __construct($data)
    {
        if (is_array($data))
        {
            $this->data = $data;
        }
        else
        {
            $this->data = [$data];
        }
    }

    public function check_array_object_type($type)
    {
        return all($this->data, function ($x) use ($type) { return is_object($x) && $x instanceof $type; });
    }
}
