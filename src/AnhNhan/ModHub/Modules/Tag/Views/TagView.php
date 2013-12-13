<?php
namespace AnhNhan\ModHub\Modules\Tag\Views;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\AbstractView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class TagView extends AbstractView
{
    private $color;
    private $text;

    public function __construct($text, $color = "")
    {
        $this->text = $text;
        $this->color = $color;
    }

    public function render()
    {
        $tag = ModHub\ht("div")
            ->addClass("tag-object");
        if ($this->color) {
            $tag->addClass("tag-color-" . $this->color);
        }
        if ($this->text) {
            $tag->setContent(ModHub\ht("span", $this->text));
        }
        return $tag;
    }
}
