<?php
namespace AnhNhan\ModHub\Modules\User\Users;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Used to track inheritance within the project.
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class AbstractUser implements AdvancedUserInterface
{
    /**
     * @deprecated
     */
    abstract public function getProfileImageRawPath();

    abstract public function getProfileImage();
}
