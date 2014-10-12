<?php
namespace AnhNhan\Converge\Storage;

/**
 * Base class for entity definitions from which entitiy definitions inherit from.
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class EntityDefinition
{
    use \AnhNhan\Converge\Infrastructure\MagicGetter;

    public function getUIDType()
    {
        throw new \Exception("This entity definition does not have a UID type!");
    }

    public function cleanId()
    {
        if (!method_exists($this, "uid")) {
            throw new \Exception("Error Processing Request");
        }
        return preg_replace("/^[A-Z]{4}-/", "", $this->uid());
    }

    public function __set($name, $value)
    {
        throw new \Exception(
            "Heads up! This object does not have any public properties! It's " .
            "all magic properties. Check if there is a method with the same name"
        );
    }
}
