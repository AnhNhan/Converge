<?php
namespace AnhNhan\Converge\Modules\Markup;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class MarkupRule extends \PhutilRemarkupRule
{
    public function getEngine()
    {
        throw new \BadMethodCallException('We don\'t use PhutilMarkupEngine!');
    }
}
