<?php
namespace AnhNhan\Converge\Views\Objects;

use AnhNhan\Converge;

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

    public function addDetail($detail, $icon = null)
    {
        $this->details[] = [
            'detail' => $detail,
            'icon' => $icon,
        ];
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function render()
    {
        $container = Converge\ht('div')->addClass('objects-object-container');

        if ($this->details)
        {
            $detailContainer = div('objects-object-details');
            foreach ($this->details as $detail)
            {
                $detail_text = $detail['detail'];
                if ($icon = idx($detail, 'icon'))
                {
                    $detail_text = Converge\icon_ion($detail_text, $icon);
                }
                $detailContainer->append(
                    div('objects-object-detail', $detail_text)
                );
            }
            $container->append($detailContainer);
        }

        $headline = Converge\ht('div')->addClass('objects-object-title');
        $headlineClass = 'objects-object-title';
        if ($this->headHref) {
            $headline = Converge\ht('a', $this->headline, array('href' => $this->headHref))
                ->addClass($headlineClass);
        } else {
            $headline = Converge\ht('div', $this->headline)->addClass($headlineClass);
        }
        $container->append($headline);

        if ($this->byline)
        {
            $container->append(
                div('objects-object-byline', $this->byline)
            );
        }

        if ($this->attributes) {
            $attributes = array_interleave(
                Converge\safeHtml(' &middot; '),
                array_filter($this->attributes)
            );

            $attributesContainer = Converge\ht('div')
                    ->addClass('objects-object-attributes');

            foreach ($attributes as $attribute) {
                $attributesContainer->append($attribute);
            }
            $container->append($attributesContainer);
        }

        if ($this->body) {
            $container->append(Converge\ht('div', $this->body))
                ->addClass('objects-object-body');
        }

        return $container;
    }
}
