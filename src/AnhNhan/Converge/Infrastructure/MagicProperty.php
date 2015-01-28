<?php
namespace AnhNhan\Converge\Infrastructure;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
trait MagicProperty
{
    public function __get($name)
    {
        if (property_exists($this, $name)) {
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
