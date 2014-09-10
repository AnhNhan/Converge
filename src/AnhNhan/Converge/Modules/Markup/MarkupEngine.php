<?php
namespace AnhNhan\Converge\Modules\Markup;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class MarkupEngine
{
    private $inputTexts = array();
    private $outputText = array();

    private $custom_rules = [];

    private $storage;

    public function __construct()
    {
        $this->storage = new MarkupTokenStorage;
    }

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

    public function setCustomRules(array $custom_rules)
    {
        assert_instances_of($custom_rules, 'PhutilRemarkupRule');
        $this->custom_rules = msort($custom_rules, 'getPriority');
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
            foreach ($this->custom_rules as $rule)
            {
                $rule->setBlockStorage($this->storage);
                $text = $rule->apply($text);
            }
            $text = static::fixupText($text);
            $this->outputText[$key] = $text;
        }

        foreach ($this->custom_rules as $rule)
        {
            $rule->didMarkupText();
        }
        foreach ($this->outputText as $key => $text)
        {
            $this->outputText[$key] = $this->storage->restore($text);
        }

        return $this;
    }

    public static function fixupText($text)
    {
        $replace_map = [
            '<p><div' => '<div',
            '</div></p>' => '</div>',
            '<p><p>' => '<p>',
            '<p><p class' => '<p class',
            '</p></p>' => '</p>',
        ];
        $text = str_replace(array_keys($replace_map), array_values($replace_map), $text);
        $text = preg_replace('/<p><p(.*?)>/', '<p$1>', $text);
        $text = preg_replace('/<p><h(\d)(.*?)>/', '<h$1$2>', $text);
        $text = preg_replace('/<\\/h(\d)><\\/p>/', '</h$1>', $text);
        return $text;
    }

    public function getOutputText($key = "default")
    {
        return $this->outputText[$key];
    }

    public function getOutputTexts()
    {
        return $this->outputText();
    }

    public static function fastParse($text, array $custom_rules = [])
    {
        $engine = new static;
        $engine->setCustomRules($custom_rules);
        return $engine->addInputText($text)->process()->getOutputText();
    }
}
