<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Objects;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
use AnhNhan\Converge\Views\Objects\Object;
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

    public function addTagObject(Tag $tag)
    {
        $this->addTagLabel($tag->label, $tag->color);
        return $this;
    }

    public function addTagLabel($label, $color)
    {
        $this->addTag(new TagView($label, $color));
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

        $that->addAttributeAsFirst(Converge\icon_ic(Converge\ht("div", $this->postCount ?: 0)->addClass("post-count"), "comments"));

        $object = $that->_parentRender();
        return $object;
    }
}
