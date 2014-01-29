<?php
namespace AnhNhan\ModHub\Modules\User\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Transaction\TransactionAwareEntityInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table(indexes={
 *   @Index(name="username", columns={"username"})
 * })
 */
class User extends EntityDefinition implements AdvancedUserInterface, TransactionAwareEntityInterface
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

    const DEFAULT_SALT_LENGTH = 22;
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
    private $profileImagePath;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @ManyToMany(targetEntity="Role")
     * @OrderBy({"name"="ASC"})
     * @var \Doctrine\ORM\PersistentCollection
     */
    private $roles = array();

    /**
     * @OneToMany(targetEntity="UserTransaction", mappedBy="object", fetch="LAZY")
     * @OrderBy({"createdAt"="ASC"})
     */
    private $xacts = array();

    /**
     * @OneToMany(targetEntity="OAuthInfo", mappedBy="user")
     * @var \Doctrine\ORM\PersistentCollection
     */
    private $oauthKeys = array();

    public function __construct()
    {
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
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

    public function setDisplayname($dname)
    {
        return $this->dispname = $dname;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function updatePassword($password, $salt)
    {
        $this->password = $password;
        $this->salt = $salt;
        return $this;
    }

    public static function generateSalt($length = self::DEFAULT_SALT_LENGTH)
    {
        return \Filesystem::readRandomCharacters($length);
    }

    public function eraseCredentials()
    {
        // Use this method with care. One wrong move and our user in the db can't log int anymore
        $this->password = null;
        $this->salt = null;
        return $this;
    }

    public function profileImageRawPath()
    {
        return $this->profileImagePath ?: self::DEFAULT_PROFILE_IMAGE;
    }

    public function profileImage()
    {
        // TODO: Once we have file objects, return them here
    }

    public function getGravatarImagePath($size = null)
    {
        return static::generateGravatarImagePath($this->uid, $size); // Uid is tmp until we have emails going
    }

    public static function generateGravatarImagePath($email, $size = null)
    {
        $hash = md5(strtolower(trim($email)));
        $url = urisprintf("//www.gravatar.com/avatar/%p?d=retro&s=%d", $hash, $size ? $size : "");
        return $url;
    }

    public function setProfileImageRawPath($path)
    {
        $this->profileImagePath = $path;
        return $this;
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

    public function createdAt()
    {
        return $this->createdAt;
    }

    public function modifiedAt()
    {
        return $this->modifiedAt;
    }

    public function updateModifiedAt()
    {
        $this->modifiedAt = new \DateTime;
        return $this;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions()
    {
        return $this->xacts;
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
