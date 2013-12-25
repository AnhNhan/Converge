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
    const UID_LENGTH_EXTENDED = 22;

    const TYPE_DEFAULT = "UIDX";

    private $uid;
    private $type;
    private $subtype;
    private $random;

    public function __construct($uid)
    {
        if (!static::checkValidity($uid)) {
            throw new \InvalidArgumentException("No valid UID given!");
        }
        $this->uid = $uid;

        $matches = array();
        preg_match("/^(?P<type>[A-Z]{4})(-(?P<subtype>[A-Z]{4}))?-(?P<random>((.*){14}|(.*){22}))$/", $uid, $matches);

        $this->type    = $matches["type"];
        $this->subtype = $matches["subtype"];
        $this->random  = $matches["random"];
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSubType()
    {
        return $this->subtype;
    }

    public function getId()
    {
        return $this->random;
    }

    public function __toString()
    {
        return $this->uid;
    }

    public static function generate($type = self::TYPE_DEFAULT, $length = null)
    {
        if (preg_match("/^[A-Z]{4}(-[A-Z]{4})?$/", $type) === 0) {
            throw new \InvalidArgumentException("Type '{$type}' is invalid!");
        }

        if ($length === null) {
            $length = (strlen($type) == 4) ? self::UID_LENGTH : self::UID_LENGTH_EXTENDED;
        } else {
            $allowedLengths = array(
                self::UID_LENGTH => true,
                self::UID_LENGTH_EXTENDED => true,
            );
            if (!isset($allowedLengths[$length])) {
                throw new \InvalidArgumentException("Provided length '{$length}' is invalid.");
            }
        }

        $random = \Filesystem::readRandomCharacters($length);

        return sprintf("%s-%s", $type, $random);
    }

    public static function generateNew($type = self::TYPE_DEFAULT, $length = null)
    {
        return new UID(self::generate($type, $length));
    }

    public static function checkValidity($uid)
    {
        return preg_match("/^[A-Z]{4}(-[A-Z]{4})?-([a-z0-9]{14}|[a-z0-9]{22})$/", $uid) === 1;
    }
}
