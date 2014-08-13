<?php
namespace AnhNhan\Converge\Views\Form\Controls;

use YamwLibs\Libs\Html\HtmlFactory as HF;
use YamwLibs\Libs\Html\Markup\HtmlTag;
use YamwLibs\Libs\Html\Markup\SafeTextNode;
use YamwLibs\Libs\View\ViewInterface;

/**
 * @method string getLabel() Gets the label
 * @method AbstractFormControl setLabel(string $label) Sets the label
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class AbstractFormControl extends HtmlTag implements ViewInterface
{
    private $label = '';

    public function __construct()
    {
        $options = array('type' => $this->getType());
        parent::__construct('input', null, $options);
        $this->addClass('form-control');
    }

    public function setName($name)
    {
        return $this->addOption('name', $name);
    }

    public function setValue($value)
    {
        return $this->addOption('value', $value);
    }

    public function render()
    {
        $this->willRender();
        $container = HF::divTag()
            ->addClass('form-control-container');

        $labelDiv = HF::divTag(new SafeTextNode('&nbsp;'))
            ->addClass('form-control-label');
        if ($this->label) {
            $labelDiv->setContent($this->label);
        }
        $container->append($labelDiv);

        $thisTag = new HtmlTag(
            $this->getTagName(),
            $this->getContent(),
            $this->getOptions()
        );
        $classes = $this->getClasses();
        if ($classes) {
            $thisTag->addClass($this->getClasses());
        }
        $id = $this->getId();
        if ($id) {
            $thisTag->setId($this->getId());
        }
        $formControlDiv = HF::divTag($thisTag)
            ->addClass('form-control-element');
        $container->append($formControlDiv);

        return $container;
    }

    public function __toString()
    {
        return (string)$this->render();
    }

    abstract protected function getType();

    protected function willRender()
    {
        return null;
    }

    public function __call($name, $arguments)
    {
        $matches = array();
        $match = preg_match(
            '/^(?P<type>g|s)et(?P<name>.*?)$/',
            $name,
            $matches
        );

        if (!$match) {
            throw new RZWebStackException('Bad Getter/Setter call');
        }

        $type = strtolower($matches['type']);
        $name = strtolower($matches['name']);
        if ($type == 'g') {
            if (!isset($this->$name)) {
                throw new RZWebStackException("Bad getter call: {$name}");
            }
            return $this->$name;
        } elseif ($type == 's') {
            if (!isset($this->$name)) {
                throw new RZWebStackException("Bad setter call: {$name}");
            }
            $this->$name = array_shift($arguments);
            return $this;
        } else {
            throw new RZWebStackException('Method does not exist!');
        }
    }
}
