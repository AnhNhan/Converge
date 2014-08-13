<?php
namespace AnhNhan\Converge\Storage\Doctrine;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Types\UID;
use Doctrine\ORM\Id\AbstractIdGenerator;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class UIDGenerator extends AbstractIdGenerator
{
    public function generate(\Doctrine\ORM\EntityManager $em, $entity)
    {
        if (!($entity instanceof EntityDefinition)) {
            throw new \Exception("Only objects of type AnhNhan\\Converge\\Storage\\EntityDefinition can be used!");
        }
        return UID::generate($entity->getUIDType());
    }
}
