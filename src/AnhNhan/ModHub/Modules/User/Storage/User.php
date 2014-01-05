<?php
namespace AnhNhan\ModHub\Modules\User\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table
 */
class User extends EntityDefinition implements AdvancedUserInterface
{
    /**
     * @Id
     * @Column(type="string")
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="AnhNhan\ModHub\Storage\Doctrine\UIDGenerator")
     */
    private $id;

    /**
     * @Column(type="string")
     */
    private $username;

    /**
     * @Column(type="string")
     */
    private $dispname;

    /**
     * @Column(type="string")
     */
    private $password;

    const DEFAULT_SALT_LENGTH = 8;
    /**
     * @Column(type="string")
     */
    private $salt;

    const DEFAULT_PROFILE_IMAGE = "/images/profile/default.png";
    /**
     * TODO: Have this point to a file object
     *
     * @Column(type="string")
     */
    private $profileImagePath = self::DEFAULT_PROFILE_IMAGE;

    /**
     * @ManyToMany(targetEntity="Role")
     * @OrderBy({"name"="ASC"})
     * @var \Doctrine\ORM\PersistentCollection
     */
    private $roles = array();

    /**
     * @OneToMany(targetEntity="OAuthInfo", mappedBy="user")
     * @var \Doctrine\ORM\PersistentCollection
     */
    private $oauthKeys = array();

    public function __construct($username, $dispName, $hashedPw, $salt)
    {
        $this->username = $username;
        $this->dispname = $dispName;
        $this->password = $hashedPw;
        $this->salt     = $salt;
    }

    public function uid()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function name()
    {
        return $this->dispname;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public static function generateSalt($length = self::DEFAULT_SALT_LENGTH)
    {
        return \Filesystem::readRandomCharacters($length);
    }

    public function eraseCredentials()
    {
        // TODO: Create pw-reset link
    }

    public function profileImageRawPath()
    {
        return $this->profileImagePath;
    }

    public function profileImage()
    {
        // TODO: Once we have file objects, return them here
    }

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function addRole(Role $role)
    {
        $this->roles[] = $role;
        return $this;
    }

    public function removeRole(Role $role)
    {
        $this->roles->remove($role->uid());
        return $this;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return true;
    }

    public function getUIDType()
    {
        return "USER";
    }
}
