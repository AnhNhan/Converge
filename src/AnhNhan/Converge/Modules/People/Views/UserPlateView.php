<?php
namespace AnhNhan\Converge\Modules\People\Views;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\People\Storage\User;
use AnhNhan\Converge\Views\AbstractView;

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
        $container = Converge\ht("div")->addClass("user-plate");
        $container->addClass("user-plate-size-" . $this->size);
        $container->addClass("user-plate-" . $this->alignment);

        $profileImagePath = $user->profileImageRawPath();
        $profileImageContainer = Converge\ht("div",
            Converge\ht("img", null, array("src" => $profileImagePath))
        )->addClass("user-plate-image-container");
        $container->append($profileImageContainer);

        $textContainer = Converge\ht("div")->addClass("user-plate-detail-container");
        $textContainer->append(Converge\ht("div", $user->name())->addClass("user-plate-username"));
        $container->append($textContainer);

        return $container;
    }
}
