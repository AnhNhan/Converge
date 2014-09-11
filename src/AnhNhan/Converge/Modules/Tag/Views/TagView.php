<?php
namespace AnhNhan\Converge\Modules\Tag\Views;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\AbstractView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class TagView extends AbstractView
{
    private $color;
    private $text;

    public function __construct($text, $color = '')
    {
        $this->text = $text;
        $this->color = $color;
    }

    public function render()
    {
        $tag = span('tag-object');
        if ($this->color) {
            $tag->addClass('tag-color-' . $this->color);
        }
        if ($this->text) {
            $tag->setContent($this->text);
        }
        return $tag;
    }
}
