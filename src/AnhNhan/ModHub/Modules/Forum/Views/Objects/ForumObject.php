<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Objects;

use AnhNhan\ModHub\Modules\Tag\Views\TagView;
use AnhNhan\ModHub\Views\Objects\Object;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class ForumObject extends Object
{
    private $tags;

    public function __construct()
    {
        parent::__construct();
        $this->tags = new MarkupContainer;
    }

    public function addTag(TagView $tag)
    {
        $this->tags->push($tag->render());
        return $this;
    }

    public function render()
    {
        if (count($this->tags)) {
            $this->addAttributeAsFirst($this->tags);
        }
        $object = parent::render();
        return $object;
    }
}
