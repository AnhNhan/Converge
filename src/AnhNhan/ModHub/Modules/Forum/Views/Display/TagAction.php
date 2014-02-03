<?php
namespace AnhNhan\ModHub\Modules\Forum\Views\Display;

use AnhNhan\ModHub as mh;
use AnhNhan\ModHub\Modules\Tag\Storage\Tag;
use AnhNhan\ModHub\Modules\Tag\Views\TagView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class TagAction extends ForumDisplayObject
{
    /**
     * @var TagView[]
     */
    protected $tags = array();

    public function addTag(Tag $tag)
    {
        $this->addTagLabel($tag->label, $tag->color);
        return $this;
    }

    public function addTagView(TagView $tagView)
    {
        $this->tags[] = $tagView;
        return $this;
    }

    public function addTagLabel($label, $color)
    {
        $this->addTagView(new TagView($label, $color));
        return $this;
    }

    public function render()
    {
        $panel = $this->buildBasicPanel();
        return $panel;
    }

    protected function getHeaderText()
    {
        return mh\hsprintf(
            "<div><strong>%s</strong> %s %s: %s</div>",
            $this->username,
            $this->getVerb(),
            count($this->tags) == 1 ? "a tag" : "tags",
            mh\safeHtml(implode("", $this->tags))
        );
    }

    abstract protected function getVerb();
}
