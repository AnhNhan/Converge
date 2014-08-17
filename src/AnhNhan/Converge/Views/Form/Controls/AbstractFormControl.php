<?php
namespace AnhNhan\Converge\Views\Form\Controls;

use AnhNhan\Converge as cv;
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

    private $text_error = '';
    private $text_help  = '';

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

    public function setHelp($text_help)
    {
        $this->text_help = $text_help;
        return $this;
    }

    /// Will cancel out help text if exists
    public function setError($text_error)
    {
        $this->text_error = $text_error;
        return $this;
    }

    public function render()
    {
        $this->willRender();
        $container = HF::divTag()
            ->addClass('form-control-container');
        if ($this->text_error)
        {
            $container->addClass('form-control-error');
        }

        $labelDiv = div('form-control-label');
        if ($this->label) {
            $labelDiv->setContent($this->label);
        }
        else
        {
            $labelDiv->setContent(cv\safeHtml('&nbsp;'));
        }
        $container->append($labelDiv);
        if ($this->text_error)
        {
            $labelDiv->append(div('form-control-label-error', $this->text_error));
        }
        else if ($this->text_help)
        {
            $labelDiv->append(div('form-control-label-help', $this->text_help));
        }

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
            throw new \Exception('Bad Getter/Setter call');
        }

        $type = strtolower($matches['type']);
        $name = strtolower($matches['name']);
        if ($type == 'g') {
            if (!isset($this->$name)) {
                throw new \Exception("Bad getter call: {$name}");
            }
            return $this->$name;
        } elseif ($type == 's') {
            if (!isset($this->$name)) {
                throw new \Exception("Bad setter call: {$name}");
            }
            $this->$name = array_shift($arguments);
            return $this;
        } else {
            throw new \Exception('Method does not exist!');
        }
    }
}
