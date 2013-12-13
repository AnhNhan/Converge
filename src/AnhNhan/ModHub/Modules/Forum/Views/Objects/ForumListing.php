<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Objects;

use AnhNhan\ModHub\Views\Objects\Listing;
use AnhNhan\ModHub\Views\Objects\AbstractObject;
use YamwLibs\Libs\Assertions\BasicAssertions as BA;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class ForumListing extends Listing
{
    public function addObject(AbstractObject $object)
    {
        BA::assertIsTypeOf($object, '\AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumObject');
        parent::addObject($object);
        return $this;
    }

    public function render()
    {
        $listing = parent::render();
        $listing->addClass("forum-list-container");
        return $listing;
    }
}
