<?php
namespace AnhNhan\ModHub\Storage;

/**
 * Base class for entity definitions from which entitiy definitions inherit from.
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class EntityDefinition
{
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
}
