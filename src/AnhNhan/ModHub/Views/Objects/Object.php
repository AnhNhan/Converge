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
    private $byline;
    private $attributes = array();
    private $details = array();
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

    public function setByLine($byline)
    {
        $this->byline = $byline;
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

    public function addDetail($detail)
    {
        $this->details[] = $detail;
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

        if ($this->details)
        {
            $detailContainer = div('objects-object-details');
            foreach ($this->details as $detail)
            {
                $detailContainer->appendContent(
                    div('objects-object-detail', $detail)
                );
            }
            $container->appendContent($detailContainer);
        }

        $headline = ModHub\ht('div')->addClass('objects-object-title');
        $headlineClass = 'objects-object-title';
        if ($this->headHref) {
            $headline = ModHub\ht('a', $this->headline, array('href' => $this->headHref))
                ->addClass($headlineClass);
        } else {
            $headline = ModHub\ht('div', $this->headline)->addClass($headlineClass);
        }
        $container->appendContent($headline);

        if ($this->byline)
        {
            $container->appendContent(
                div('objects-object-byline', $this->byline)
            );
        }

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
