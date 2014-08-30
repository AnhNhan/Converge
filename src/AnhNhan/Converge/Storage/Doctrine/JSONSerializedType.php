<?php
namespace AnhNhan\Converge\Storage\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class JSONSerializedType extends Type
{
    const JsonObject = 'json_object_array';

    public function getName()
    {
        return self::JsonObject;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return (array) json_decode($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return json_encode($value);
    }
}
