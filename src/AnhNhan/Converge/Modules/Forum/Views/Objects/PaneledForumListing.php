<?php
namespace AnhNhan\Converge\Modules\Forum\Views\Objects;

use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
use AnhNhan\Converge\Views\Panel\Panel;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class PaneledForumListing extends ForumListing
{
    private $panelHeader;
    private $tags = array();

    // @Override, we handle title ourself
    public function setTitle($title)
    {
        $this->panelHeader = $title;
        return $this;
    }

    public function addTag($tag)
    {
        if ($tag instanceof Tag) {
            $tag = new TagView($tag->label(), $tag->color());
        } else if (!($tag instanceof TagView)) {
            throw new \InvalidArgumentException;
        }

        $this->tags[] = $tag;
        return $this;
    }

    public function render()
    {
        $panel = new Panel;
        $panel->addClass("panel-forum-listing");
        $panel->setHeader($this->panelHeader);
        $panel->append(parent::render());

        if ($this->tags) {
            foreach ($this->tags as $tag) {
                $panel->midriff()->push($tag);
            }
        }

        return $panel;
    }
}
