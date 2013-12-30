<?php
namespace AnhNhan\ModHub\Modules\Markup;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class MarkupEngine
{
    private $inputTexts = array();
    private $outputText = array();

    public function addInputText($text, $key = "default")
    {
        if (isset($this->inputTexts[$key])) {
            throw new \Exception("Input $key already exists. Can't add it again!");
        }

        $this->inputTexts[$key] = $text;
        return $this;
    }

    public function process()
    {
        $parsedown = \Parsedown::instance();
        foreach ($this->inputTexts as $key => $input) {
            $this->outputText[$key] = $parsedown->parse($input);
        }

        return $this;
    }

    public function getOutputText($key = "default")
    {
        return $this->outputText[$key];
    }

    public function getOutputTexts()
    {
        return $this->outputText();
    }

    public static function fastParse($text)
    {
        $engine = new static;
        return $engine->addInputText($text)->process()->getOutputText();
    }
}
