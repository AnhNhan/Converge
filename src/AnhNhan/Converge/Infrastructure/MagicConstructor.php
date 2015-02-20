<?php
namespace AnhNhan\Converge\Infrastructure;

/**
 * Generates and
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
trait MagicConstructor
{
    // TODO: Cache constructor per class
    // TODO: Typecheck properties from docblock annotations?
    public function __construct()
    {
        $args = func_get_args();
        $properties = mpull((new \ReflectionClass(get_class($this)))->getProperties(), 'getName');

        if (count($args) != count($properties))
        {
            throw new \RuntimeException(sprintf('Constructor invocation for %s had wrong number of arguments. (expected %d, got %d)', get_class($this), count($properties), count($args)));
        }

        foreach ($properties as $ii => $property)
        {
            $this->$property = $args[$ii];
        }
    }
}
