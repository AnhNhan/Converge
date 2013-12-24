<?php
namespace AnhNhan\ModHub\Storage\Doctrine;

use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Types\UID;
use Doctrine\ORM\Id\AbstractIdGenerator;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class UIDGenerator extends AbstractIdGenerator
{
    public function generate(\Doctrine\ORM\EntityManager $em, $entity)
    {
        if (!($entity instanceof EntityDefinition)) {
            throw new \Exception("Only objects of type AnhNhan\\ModHub\\Storage\\EntityDefinition can be used!");
        }
        return UID::generateNew($entity->getUIDType());
    }
}
