<?php
namespace AnhNhan\Converge\Infrastructure;

trait MagicGetter
{
    // Magic properties :)
    // $this->app instead of $this->app()
    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        }

        throw new \RunTimeException(sprintf(
            "Ayo, you tried to access '%s::%s' that does not exist in here!\n" .
            "Better check your code!",
            get_class($this),
            $name
        ));
    }
}
