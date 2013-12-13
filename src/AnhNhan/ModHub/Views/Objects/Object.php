<?php
namespace AnhNhan\ModHub\Views\Objects;

use AnhNhan\ModHub;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Object extends AbstractObject
{
    private $headline;
    private $headHref;
    private $attributes = [];
    private $body;

    public function setHeadline($headline)
    {
        $this->headline = $headline;
        return $this;
    }

    public function setHeadHref($href)
    {
        $this->headHref = $href;
        return $this;
    }

    public function addAttribute($attr)
    {
        $this->attributes[] = $attr;
        return $this;
    }

    protected function addAttributeAsFirst($attr)
    {
        array_unshift($this->attributes, $attr);
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function render()
    {
        $container = ModHub\ht('div')->addClass('objects-object-container');

        $headline = ModHub\ht('div')->addClass('objects-object-title');
        $headlineClass = 'objects-object-title';
        if ($this->headHref) {
            $headline = ModHub\ht('a', $this->headline, ['href' => $this->headHref])
                ->addClass($headlineClass);
        } else {
            $headline = ModHub\ht('div', $this->headline)->addClass($headlineClass);
        }
        $container->appendContent($headline);

        if ($this->attributes) {
            $attributes = array_interleave(
                ModHub\safeHtml(' &middot; '),
                array_filter($this->attributes)
            );

            $attributesContainer = ModHub\ht('div')
                    ->addClass('objects-object-attributes');

            foreach ($attributes as $attribute) {
                $attributesContainer->appendContent($attribute);
            }
            $container->appendContent($attributesContainer);
        }

        if ($this->body) {
            $container->appendContent(ModHub\ht('div', $this->body))
                ->addClass('objects-object-body');
        }

        return $container;
    }
}
