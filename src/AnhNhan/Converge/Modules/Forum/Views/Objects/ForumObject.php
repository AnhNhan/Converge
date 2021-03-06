<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Objects;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Tag\Storage\Tag;
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

    public function addTag($tag)
    {
        $this->tags->push($tag);
        return $this;
    }

    public function addTagObject(Tag $tag)
    {
        $this->addTag(link_tag($tag, TagLinkExtra_None));
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

        $that->addAttributeAsFirst(Converge\icon_ion(Converge\ht("div", $this->postCount ?: 0)->addClass("post-count"), "chatbubbles"));

        $object = $that->_parentRender();
        return $object;
    }
}
