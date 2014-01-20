<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Objects;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Tag\Views\TagView;
use AnhNhan\ModHub\Views\Objects\Object;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class ForumObject extends Object
{
    private $tags;
    private $postCount;

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

    public function postCount($postCount = null) {
        if ($postCount === null) {
            return $this->postCount;
        } else {
            $this->postCount = $postCount;
            return $this;
        }
    }

    private function _parentRender()
    {
        return parent::render();
    }

    public function render()
    {
        // Hack so we won't add tags multiple times when re-rendering
        $that = clone $this;
        if (count($this->tags)) {
            $that->addAttributeAsFirst($this->tags);
        }

        if ($this->postCount !== null) {
            $that->addAttributeAsFirst(ModHub\icon_bs2(ModHub\ht("div", $this->postCount)->addClass("post-count"), "th-list", false));
        }

        $object = $that->_parentRender();
        return $object;
    }
}
