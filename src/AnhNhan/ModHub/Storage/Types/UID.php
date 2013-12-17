<?php
namespace AnhNhan\ModHub\Storage\Types;

/**
 * Something of the format XXXX-idsjdibv
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class UID
{
    const UID_LENGTH = 14;

    const NAME_DEFAULT = "UIDX";

    private $uid;
    private $name;
    private $random;

    public function __construct($uid)
    {
        if (!static::checkValidity($uid)) {
            throw new \InvalidArgumentException("No valid UID given!");
        }
        $this->uid = $uid;

        $matches = array();
        preg_match("/^(?P<name>[A-Z]{4})-(?P<random>(.*){14})$/", $uid, $matches);

        $this->name = $matches["name"];
        $this->random = $matches["random"];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->random;
    }

    public static function generate($name = self::NAME_DEFAULT)
    {
        if (preg_match("/^[A-Z]{4}$/", $name) === 0) {
            throw new \InvalidArgumentException("\$name should be 4 characters long!");
        }

        $random = \Filesystem::readRandomCharacters(self::UID_LENGTH);

        return sprintf("%s-%s", strtoupper($name), $random);
    }

    public static function checkValidity($uid)
    {
        return preg_match("/^[A-Z]{4}-[a-z0-9]{14}$/", $uid) === 1;
    }
}
