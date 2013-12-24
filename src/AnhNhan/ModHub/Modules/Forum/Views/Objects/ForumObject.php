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
    private $tagsAdded = false;

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

    public function render()
    {
        if (!$this->tagsAdded) {
            if (count($this->tags)) {
                $this->addAttributeAsFirst($this->tags);
            }
            
            $this->addAttributeAsFirst(ModHub\icon_text(ModHub\ht("div", $this->postCount, array("style" => "display: inline-block; min-width: 1em;")), "th-list", false));
            
            $this->tagsAdded = true;
        }

        $object = parent::render();
        return $object;
    }
}
