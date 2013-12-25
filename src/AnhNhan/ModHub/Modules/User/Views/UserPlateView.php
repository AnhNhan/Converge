<?php
namespace AnhNhan\ModHub\Modules\User\Views;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\User\Storage\User;
use AnhNhan\ModHub\Views\AbstractView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class UserPlateView extends AbstractView
{
    private $user;

    const IMAGE_ALIGN_LEFT  = "left";
    const IMAGE_ALIGN_RIGHT = "right";
    private $alignment = self::IMAGE_ALIGN_LEFT;

    const SIZE_SMALL  = "sm";
    const SIZE_MEDIUM = "md";
    const SIZE_LARGE  = "lg";
    private $size = self::SIZE_MEDIUM;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function setAlignment($alignment = self::IMAGE_ALIGN_LEFT)
    {
        $this->alignment = $alignment;
        return $this;
    }

    public function setSize($size = self::SIZE_MEDIUM)
    {
        $this->size = $size;
        return $this;
    }

    public function render()
    {
        $user = $this->user;
        $container = ModHub\ht("div")->addClass("user-plate");
        $container->addClass("user-plate-size-" . $this->size);
        $container->addClass("user-plate-" . $this->alignment);

        $profileImagePath = $user->profileImageRawPath();
        $profileImageContainer = ModHub\ht("div",
            ModHub\ht("img", null, array("src" => $profileImagePath))
        )->addClass("user-plate-image-container");
        $container->appendContent($profileImageContainer);

        $textContainer = ModHub\ht("div")->addClass("user-plate-detail-container");
        $textContainer->appendContent(ModHub\ht("div", $user->name())->addClass("user-plate-username"));
        $container->appendContent($textContainer);

        return $container;
    }
}
