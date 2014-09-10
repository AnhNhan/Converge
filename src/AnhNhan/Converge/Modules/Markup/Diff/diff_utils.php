<?php

namespace diff\utils;

/// Derived from PhutilRemarkupBlockStorage
class MarkupDiffBlockStorage
{
    // Using \2 since \1 is used by the diff renderer
    const MAGIC_BYTE = "\2";
    const MAGIC_TERM = "Z";

    private $map = [];
    private $index;

    public function store($piece)
    {
        if (isset($this->map_inverse[$piece]))
        {
            $key = $this->map_inverse[$piece];
        }
        else
        {
            $key = self::MAGIC_BYTE . (++$this->index) . self::MAGIC_TERM;
            $this->map[$key] = $piece;
            $this->map_inverse[$piece] = $key;
        }
        return $key;
    }

    public function restore($big_text)
    {
        if ($this->map)
        {
            $big_text = str_replace(array_reverse(array_keys($this->map)), array_reverse($this->map), $big_text);
        }
        return $big_text;
    }

    public function overwrite($key, $piece)
    {
        assert_stringlike($piece);
        $piece = (string) $piece;
        unset($this->map_inverse[$this->map[$key]]);
        $this->map_inverse[$piece] = $key;

        $this->map[$key] = $piece;
        return $this;
    }
}

global $block_storage;

function _static_init()
{
    global $block_storage;
    if (!$block_storage)
    {
        $block_storage = new MarkupDiffBlockStorage;
    }
}

function save_html($text)
{
    global $block_storage;
    _static_init();

    $store_fun = function ($matches) use ($block_storage)
    {
        return $block_storage->store($matches[0]);
    };

    $text = preg_replace_callback(
        '/<.*?>/',
        $store_fun,
        $text
    );
    return $text;
}

function restore_html($text)
{
    global $block_storage;
    return $block_storage->restore($text);
}
