<?php
namespace AnhNhan\Converge\Web\Application;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class AbstractPayload
{
    private $payloadContent;

    private $payloadFlags = array();

    final public function __construct($contents = null, $flags = array())
    {
        $this->payloadContent = $contents;
        $this->payloadFlags = array();
    }

    final public function setFlag($key, $value)
    {
        $this->payloadFlags[$key] = $value;
        return $this;
    }

    final public function getFlag($key)
    {
        return idx($this->payloadFlags, $key);
    }

    final protected function getFlags()
    {
        return $this->payloadFlags;
    }

    final public function setPayloadContents($contents)
    {
        $this->payloadContent = $contents;
        return $this;
    }

    final protected function getPayloadContents()
    {
        return $this->payloadContent;
    }

    abstract public function render();

    final public function __toString()
    {
        return (string)$this->render();
    }
}
