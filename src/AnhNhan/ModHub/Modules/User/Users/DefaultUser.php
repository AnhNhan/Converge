<?php
namespace AnhNhan\ModHub\Modules\User\Users;

/**
 * Crude implementation of interfaces until we get something up and running
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DefaultUser extends AbstractUser
{
    private $username;
    private $dispname;
    private $password;
    private $salt;

    // Custom stuff

    const DEFAULT_PROFILE_IMAGE = "/images/profile/default.png";
    private $profile_image_path = self::DEFAULT_PROFILE_IMAGE;

    public function __construct($username, $dispname, $password, $salt)
    {
        $this->username = $username;
        $this->dispname = $dispname;
        $this->password = $password;
        $this->salt = $salt;
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

    public function eraseCredentials()
    {
        // TODO: Do this once we have credentials
    }

    public function getProfileImageRawPath()
    {
        return $this->profile_image_path;
    }

    public function getProfileImage()
    {
        // TODO: Once we have file objects, return them here
    }

    public function getRoles()
    {
        return array(
            "ROLE_USER",
        );
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        // TODO: Change this as soon as we have that feature
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
}
