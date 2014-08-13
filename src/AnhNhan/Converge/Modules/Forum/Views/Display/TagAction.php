<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Display;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\AbstractView;
use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Modules\Tag\Views\TagView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class TagAction extends AbstractView
{
    use TChangeObject;

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
        return cv\hsprintf(
            "<div><strong>%s</strong> %s %s: %s</div>",
            $this->username,
            $this->getVerb(),
            count($this->tags) == 1 ? "a tag" : "tags",
            cv\safeHtml(implode("", $this->tags))
        );
    }

    abstract protected function getVerb();
}
