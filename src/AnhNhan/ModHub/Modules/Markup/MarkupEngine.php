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

        // Replace opening braces
        $text = str_replace('<', '&lt;', $text);

        // Replace closing braces that are not the first (or repeated) on the line
        $text = preg_replace('/^([^\s>]+)(>)/', '$1&gt;', $text);

        $this->inputTexts[$key] = $text;
        return $this;
    }

    public function process()
    {
        $parsedown = \Parsedown::instance();
        foreach ($this->inputTexts as $key => $input) {
            $text = $parsedown->parse($input);
            // Decoding double-escapes thanks to above. DANGER: Insecure??
            // Tests have been done with `&gt; x >`, `>< a>>`, `<i>hi</i>` and `> x >`, they're proof - for now.
            $text = preg_replace('/&amp;([\w\d]{1,6};)/', '&$1', $text);
            $this->outputText[$key] = $text;
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
